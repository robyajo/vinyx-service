function createSocketHandler(io, tiktokClient, logger, configManager, serverInfo) {
  io.on("connection", (socket) => {
    console.log("Client connected to socket");
    socket.emit("audio_config", configManager.getAudioConfig());
    socket.emit("server_info", {
      port: serverInfo.port,
      env_port: serverInfo.envPort,
    });

    socket.on("set_session", (data) => {
      console.log(
        `Session started by: ${data.staff_name} on ${data.platform}`,
      );
      logger.setSession(data);
    });

    socket.on("set_target", (newUsername) => {
      console.log(`Manual switch to: ${newUsername}`);
      tiktokClient.setCurrentUsername(newUsername);
      tiktokClient.connectToTikTok(newUsername);
    });
  });
}

module.exports = { createSocketHandler };
