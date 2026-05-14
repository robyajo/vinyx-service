const axios = require("axios");

function createLogger(apiUrl) {
  let sessionData = {
    staff_name: "System",
    platform: "tiktok",
  };

  function setSession(data) {
    sessionData = {
      staff_name: data.staff_name,
      platform: data.platform,
    };
  }

  async function logToLaravel(type, data, currentUsername) {
    try {
      await axios.post(`${apiUrl}/api/live-activities`, {
        platform: sessionData.platform || "tiktok",
        username: currentUsername,
        type: type,
        nickname: data.nickname || data.uniqueId,
        content: data.comment || data.giftName || null,
        count: data.repeatCount || 1,
        metadata: {
          tiktok_data: data,
          host_name: sessionData.staff_name,
        },
      });
    } catch (err) {
      console.error(
        "Failed to log activity to Laravel:",
        err.response?.data?.message || err.message,
      );
    }
  }

  return { logToLaravel, setSession };
}

module.exports = { createLogger };
