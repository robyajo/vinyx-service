require("dotenv").config();
const express = require("express");
const http = require("http");
const { Server } = require("socket.io");

const { createConfigManager } = require("./config");
const { createLogger } = require("./logger");
const { createTikTokClient } = require("./tiktok-client");
const { createSocketHandler } = require("./socket-handler");

const app = express();
const server = http.createServer(app);

const io = new Server(server, {
  cors: {
    origin: "*",
  },
});

const API_URL = process.env.API_URL;
const configManager = createConfigManager(API_URL);
const logger = createLogger(API_URL);
const tiktokClient = createTikTokClient(io, logger, configManager);

const serverInfo = { port: null, envPort: process.env.PORT || null };
createSocketHandler(io, tiktokClient, logger, configManager, serverInfo);

async function startServer() {
  try {
    console.log("Fetching config from Laravel...");
    const config = await configManager.fetchConfig();
    const { tiktok_username, listener_port, is_active } = config;

    tiktokClient.setCurrentUsername(tiktok_username);

    const port = process.env.PORT || listener_port;
    serverInfo.port = port;

    console.log(`Config loaded:`);
    console.log(`  TikTok Username : ${tiktok_username}`);
    console.log(`  Port (Laravel)  : ${listener_port}`);
    console.log(`  Port (.env)     : ${process.env.PORT}`);
    console.log(`  Port (used)     : ${port}`);
    console.log(`  Active          : ${is_active}`);

    io.emit("server_info", {
      port,
      env_port: process.env.PORT || null,
    });

    if (is_active) {
      tiktokClient.connectToTikTok(tiktok_username);
    }

    server.listen(port, () => {
      console.log(`TikTok Listener Server running on http://localhost:${port}`);
    });

    setInterval(async () => {
      try {
        const newConfig = await configManager.fetchConfig();
        io.emit("audio_config", configManager.getAudioConfig());

        if (
          newConfig.tiktok_username !== tiktokClient.getCurrentUsername() &&
          newConfig.is_active
        ) {
          console.log(`Detected username change: ${newConfig.tiktok_username}`);
          tiktokClient.setCurrentUsername(newConfig.tiktok_username);
          io.emit("clear_chat");
          tiktokClient.connectToTikTok(tiktokClient.getCurrentUsername());
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
