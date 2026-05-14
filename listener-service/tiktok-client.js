const { WebcastPushConnection } = require("tiktok-live-connector");

function createTikTokClient(io, logger, configManager) {
  let tiktokLive = null;
  let currentUsername = "";

  function connectToTikTok(username) {
    if (tiktokLive) {
      tiktokLive.disconnect();
    }

    if (!username || username === "username_default") {
      console.log("No valid username provided, waiting...");
      io.emit("system_msg", { text: "Waiting for TikTok username..." });
      return;
    }

    const cleanUsername = username.startsWith("@")
      ? username.substring(1)
      : username;
    console.log(`[TikTok] Attempting to connect to: ${cleanUsername}...`);
    io.emit("system_msg", { text: `Connecting to ${cleanUsername}...` });

    tiktokLive = new WebcastPushConnection(cleanUsername, {
      processInitialData: false,
      enableExtendedGiftInfo: true,
    });

    tiktokLive
      .connect()
      .then((state) => {
        const roomId = state.roomId;
        console.log(`[TikTok] ✅ Connected to Room ID: ${roomId}`);
        io.emit("tiktok_connected", { roomId, username: cleanUsername });
        io.emit("system_msg", {
          text: `✅ Connected to ${cleanUsername} (Room: ${roomId})`,
        });
      })
      .catch((err) => {
        const errorMsg = err?.message || err || "Unknown Error";
        console.error("[TikTok] ❌ Connection Failed:", errorMsg);

        let displayMsg = errorMsg;
        if (typeof errorMsg === "string" && errorMsg.includes("200")) {
          displayMsg =
            "TikTok blocked connection (Error 200). Check if account is LIVE or try again later.";
        }

        io.emit("system_msg", { text: `❌ ${displayMsg}` });

        console.log(`[TikTok] Retrying ${username} in 10s if still current...`);
        setTimeout(() => {
          if (username === currentUsername) {
            connectToTikTok(username);
          }
        }, 60000);
      });

    tiktokLive.on("member", (data) => {
      console.log(`[Join] ${data.uniqueId} joined the room`);
      logger.logToLaravel("join", data, currentUsername);
      io.emit("viewer_join", {
        username: data.uniqueId,
        nickname: data.nickname,
        audio: {
          sound: configManager.getAudioConfig().new_user_sound,
          enabled: configManager.getAudioConfig().new_user_sound_enabled,
        },
      });
    });

    tiktokLive.on("roomUser", (data) => {
      io.emit("viewer_count", {
        count: data.viewerCount,
      });
    });

    tiktokLive.on("social", (data) => {
      console.log(`[Social] ${data.uniqueId} performed: ${data.displayType}`);
      logger.logToLaravel("social", data, currentUsername);
      io.emit("social", {
        username: data.uniqueId,
        nickname: data.nickname,
        displayType: data.displayType,
      });
    });

    tiktokLive.on("chat", (data) => {
      console.log(`[Chat] ${data.uniqueId}: ${data.comment}`);
      logger.logToLaravel("chat", data, currentUsername);
      io.emit("new_chat", {
        username: data.uniqueId,
        nickname: data.nickname,
        comment: data.comment,
        profilePicture: data.profilePictureUrl,
        audio: {
          sound: configManager.getAudioConfig().chat_sound,
          enabled: configManager.getAudioConfig().chat_sound_enabled,
        },
      });
    });

    tiktokLive.on("gift", (data) => {
      console.log(
        `[Gift] 🎁 ${data.uniqueId} sent ${data.giftName} x${data.repeatCount}`,
      );
      logger.logToLaravel("gift", data, currentUsername);
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

  function setCurrentUsername(username) {
    currentUsername = username;
  }

  function getCurrentUsername() {
    return currentUsername;
  }

  return { connectToTikTok, setCurrentUsername, getCurrentUsername };
}

module.exports = { createTikTokClient };
