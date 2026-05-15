const axios = require("axios");

function createConfigManager(apiUrl) {
    let currentAudioConfig = {
        new_user_sound: "/storage/sounds/new-user.mp3",
        new_user_sound_enabled: true,
        chat_sound: "/storage/sounds/chat.mp3",
        chat_sound_enabled: true,
        tts_active: true,
        tts_voice: "female",
    };

    async function fetchConfig() {
        const response = await axios.get(`${apiUrl}/api/stream-config`);
        const data = response.data;

        currentAudioConfig = {
            new_user_sound: data.new_user_sound,
            new_user_sound_enabled: data.new_user_sound_enabled,
            chat_sound: data.chat_sound,
            chat_sound_enabled: data.chat_sound_enabled,
            tts_active: data.tts_active,
            tts_voice: data.tts_voice,
        };

        return data;
    }

    function getAudioConfig() {
        return { ...currentAudioConfig };
    }

    return { fetchConfig, getAudioConfig };
}

module.exports = { createConfigManager };
