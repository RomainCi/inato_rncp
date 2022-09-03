window._ = require("lodash");

try {
    require("bootstrap");
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require("axios");
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.withCredentials = true;
axios.defaults.baseURL = "http://127.0.0.1:8000";

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from "laravel-echo";
import { useRoute } from "vue-router";

window.io = require("socket.io-client");

window.Echo = new Echo({
    broadcaster: "socket.io",
    host: window.location.hostname + ":6001",
    auth: {
        headers: {
            "X-CSRF-TOKEN": "{{csrf_token()}}",
        },
    },
});
// window.Echo = new Echo({
//     broadcaster: "socket.io",
//     host: window.location.hostname + ":6001",
//     //     authEndpoint: null,
//     authorizer: (channel, options) => {
//         console.log(channel, "channel");
//         return {
//             authorize: (socketId, callback) => {
//                 axios
//                     .post("/api/broadcasting/auth", {
//                         socket_id: socketId,
//                         channel_name: channel.name,
//                     })
//                     .then((response) => {
//                         callback(false, response.data);
//                     })
//                     .catch((error) => {
//                         callback(true, error);
//                     });
//             },
//         };
//     },
// });

// window.Echo.channel("projet").listen(".chat-message", (e) => {
//     console.log(e);
// });
// window.Pusher = require("pusher-js");

// window.Echo = new Echo({
//     broadcaster: "pusher",
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true,
// });

// console.log(window.Echo.channel("chat").listen(".chat-message", (event) => {}));
