module.exports = {
  apps: [
    {
      name: "tiktok-live-agent",
      cwd: "/srv/mituni/node/stream.mituni.cloud/live-stream-agent",
      script: "npm",
      args: "start",
      env: {
        NODE_ENV: "production",
        PORT: 9090
      }
    },
  ]
};
