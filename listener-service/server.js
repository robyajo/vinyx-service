require("dotenv").config();
const express = require("express");
const http = require("http");
const { Server } = require("socket.io");
const { WebcastPushConnection } = require("tiktok-live-connector");
const axios = require("axios"); // Pastikan sudah npm install axios

const app = express();
const server = http.createServer(app);

const io = new Server(server, {
  cors: {
    origin: "*",
  },
});

// URL API Laravel Anda
const LARAVEL_API_URL = `${process.env.API_URL}/api/live-config`;

let currentUsername = "";
let audioConfig = {
  new_user_sound: "/storage/sounds/new-user.mp3",
  new_user_sound_enabled: true,
  chat_sound: "/storage/sounds/chat.mp3",
  chat_sound_enabled: true,
  tts_active: true,
  tts_voice: "female",
};

async function fetchConfig() {
  const response = await axios.get(LARAVEL_API_URL);
  const data = response.data;

  audioConfig = {
    new_user_sound: data.new_user_sound,
    new_user_sound_enabled: data.new_user_sound_enabled,
    chat_sound: data.chat_sound,
    chat_sound_enabled: data.chat_sound_enabled,
    tts_active: data.tts_active,
    tts_voice: data.tts_voice,
  };

  return data;
}

async function startServer() {
  try {
    console.log("Fetching config from Laravel...");
    const config = await fetchConfig();
    const { tiktok_username, listener_port, is_active } = config;
    currentUsername = tiktok_username;

    console.log(
      `Config loaded: Username=${tiktok_username}, Port=${listener_port}, Active=${is_active}`,
    );

    let tiktokLive;
    let sessionData = {
      staff_name: "System",
      platform: "tiktok"
    };

    async function logToLaravel(type, data) {
      try {
        await axios.post(`${process.env.API_URL}/api/live-activities`, {
          platform: sessionData.platform || "tiktok",
          username: currentUsername,
          type: type,
          nickname: data.nickname || data.uniqueId,
          content: data.comment || data.giftName || null,
          count: data.repeatCount || 1,
          metadata: {
            // Kita bungkus data asli TikTok dan tambahkan info host
            tiktok_data: data,
            host_name: sessionData.staff_name
          }
        });
      } catch (err) {
        console.error("Failed to log activity to Laravel:", err.response?.data?.message || err.message);
      }
    }

    function connectToTikTok(username) {
      if (tiktokLive) {
        tiktokLive.disconnect();
      }

      if (!username || username === "username_default") {
        console.log("No valid username provided, waiting...");
        io.emit("system_msg", { text: "Waiting for TikTok username..." });
        return;
      }

      // Bersihkan tanda @ jika ada
      const cleanUsername = username.startsWith('@') ? username.substring(1) : username;
      console.log(`[TikTok] Attempting to connect to: ${cleanUsername}...`);
      io.emit("system_msg", { text: `Connecting to ${cleanUsername}...` });

      tiktokLive = new WebcastPushConnection(cleanUsername, {
        processInitialData: false,
        enableExtendedGiftInfo: true
      });

      tiktokLive
        .connect()
        .then((state) => {
          console.log(`[TikTok] ✅ Connected to Room ID: ${state.roomId}`);
          io.emit("system_msg", { text: `✅ Connected to ${cleanUsername}` });
        })
        .catch((err) => {
          const errorMsg = err?.message || err || "Unknown Error";
          console.error("[TikTok] ❌ Connection Failed:", errorMsg);
          
          let displayMsg = errorMsg;
          if (typeof errorMsg === 'string' && errorMsg.includes("200")) {
            displayMsg = "TikTok blocked connection (Error 200). Check if account is LIVE or try again later.";
          }
          
          io.emit("system_msg", { text: `❌ ${displayMsg}` });
          
          // Retry after 10 seconds only if username is still current
          console.log(`[TikTok] Retrying ${username} in 10s if still current...`);
          setTimeout(() => {
            if (username === currentUsername) {
              connectToTikTok(username);
            }
          }, 60000);
        });

      tiktokLive.on("member", (data) => {
        console.log(`[Join] ${data.uniqueId} joined the room`);
        logToLaravel("join", data);
        io.emit("viewer_join", {
          username: data.uniqueId,
          nickname: data.nickname,
          audio: {
            sound: audioConfig.new_user_sound,
            enabled: audioConfig.new_user_sound_enabled,
          },
        });
      });

      // Event Update jumlah viewer
      tiktokLive.on("roomUser", (data) => {
        io.emit("viewer_count", {
          count: data.viewerCount
        });
      });

      // Event Follow/Share juga bisa dianggap sebagai interaksi "Join" atau sosial
      tiktokLive.on("social", (data) => {
        console.log(`[Social] ${data.uniqueId} performed: ${data.displayType}`);
        logToLaravel("social", data);
        io.emit("social", {
          username: data.uniqueId,
          nickname: data.nickname,
          displayType: data.displayType
        });
      });

      tiktokLive.on("chat", (data) => {
        console.log(`[Chat] ${data.uniqueId}: ${data.comment}`);
        logToLaravel("chat", data);
        io.emit("new_chat", {
          username: data.uniqueId,
          nickname: data.nickname,
          comment: data.comment,
          profilePicture: data.profilePictureUrl,
          audio: {
            sound: audioConfig.chat_sound,
            enabled: audioConfig.chat_sound_enabled,
          },
        });
      });

      tiktokLive.on("gift", (data) => {
        console.log(`[Gift] 🎁 ${data.uniqueId} sent ${data.giftName} x${data.repeatCount}`);
        logToLaravel("gift", data);
        io.emit("new_gift", {
          username: data.uniqueId,
          giftName: data.giftName,
          repeatCount: data.repeatCount,
          giftIcon: data.giftPictureUrl,
        });
      });

      tiktokLive.on("disconnected", () => {
        console.log("[TikTok] Disconnected from TikTok Live.");
        io.emit("system_msg", { text: "⚠️ Disconnected from TikTok" });
      });

      tiktokLive.on("error", (err) => {
        const errorMsg = err?.message || err || "Unknown Error";
        console.error("[TikTok] Error:", errorMsg);
      });
    }

    // Jalankan koneksi awal jika aktif
    if (is_active) {
      connectToTikTok(tiktok_username);
    }

    io.on("connection", (socket) => {
      console.log("Client connected to socket");
      socket.emit("audio_config", audioConfig);

      socket.on("set_session", (data) => {
        console.log(`Session started by: ${data.staff_name} on ${data.platform}`);
        sessionData = {
          staff_name: data.staff_name,
          platform: data.platform
        };
      });

      socket.on("set_target", (newUsername) => {
        console.log(`Manual switch to: ${newUsername}`);
        currentUsername = newUsername;
        connectToTikTok(newUsername);
      });
    });

    server.listen(listener_port, () => {
      console.log(
        `TikTok Listener Server running on http://localhost:${listener_port}`,
      );
    });

    // Refresh config every 60 seconds
    setInterval(async () => {
      try {
        const newConfig = await fetchConfig();
        io.emit("audio_config", audioConfig);

        // Jika username berubah di Laravel, otomatis pindah target
        if (newConfig.tiktok_username !== currentUsername && newConfig.is_active) {
          console.log(`Detected username change: ${newConfig.tiktok_username}`);
          currentUsername = newConfig.tiktok_username;
          
          // Bersihkan chat lama di dashboard
          io.emit("clear_chat");
          
          connectToTikTok(currentUsername);
        }
        
        console.log("Config refreshed from Laravel");
      } catch (err) {
        console.error("Failed to refresh config", err.message);
      }
    }, 60000);
  } catch (error) {
    console.error(
      "Failed to start server: Could not fetch config from Laravel.",
      error.message,
    );
    console.log("Retrying in 5 seconds...");
    setTimeout(startServer, 5000);
  }
}

startServer();
