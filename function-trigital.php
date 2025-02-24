<?php
/**
 * Theme functions and definitions.
 */
function sala_child_enqueue_styles()
{

    if (SCRIPT_DEBUG) {
        wp_enqueue_style('sala-style', get_template_directory_uri() . '/style.css');
    }

    wp_enqueue_style(
        'sala-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('sala-style'),
        wp_get_theme()->get('Version')
    );
}

add_action('wp_enqueue_scripts', 'sala_child_enqueue_styles');
function add_chatbot_to_footer()
{
    ?>
    <!-- Chatbot HTML -->

    <body>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <div id="chatbotContainer">
            <button id="chatbotButton" aria-label="Open Chatbot">
                <img src="https://trigitaltech.com/wp-content/uploads/2024/12/chatbot-icon.svg" alt="chatbot-icon" />
            </button>
            <div id="chatContainer" class="hidden">
                <div id="chatHeader">
                    <img src="https://trigitaltech.com/wp-content/uploads/2024/12/chatbot-icon.svg" alt="chatbot-icon" />
                    <h4>Trigo AI Assistant</h4>
                    <button id="closeChat">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="chatBody">
                    <div id="userFormContainer">
                        <form id="userForm">
                            <div class="form-group">
                                <label for="name"><b>Name:</b></label>
                                <input type="text" id="name" required />
                            </div>
                            <div class="form-group">
                                <label for="email"><b>Email:</b></label>
                                <input type="email" id="email" required />
                            </div>
                            <div class="form-group">
                                <label for="company"><b>Company:</b></label>
                                <input type="text" id="company" required />
                            </div>
                            <div class="form-group">
                                <label for="jobTitle"><b>Job Title:</b></label>
                                <input type="text" id="jobTitle" required />
                            </div>
                            <button class="form-submit-btn" type="submit">Submit</button>
                            <button class="form-submit-btn" id="existingUserButton">
                                Are you an existing user?
                            </button>
                        </form>
                    </div>
                </div>
                <div id="chatFooter" class="hidden">
                    <input type="text" id="chatInput" placeholder="Type your message..." />
                    <button id="sendMessage"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    </body>
    <!-- Chatbot CSS -->
    <style>
        body {
            margin: 0 !important;
            font-family: "Roboto", sans-serif !important;
            background-color: #f4f4f9 !important;
        }

        #chatbotButton {
            position: fixed !important;
            bottom: 20px !important;
            right: 20px !important;
            background: linear-gradient(45deg, #ff5a00, #ff9c40) !important;
            color: white !important;
            border: none !important;
            border-radius: 50% !important;
            padding: 17px !important;
            font-size: 24px !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2) !important;
            z-index: 1000 !important;
            transition: all 0.3s ease !important;
        }

        #chatbotButton img {
            height: 40px !important;
            width: 40px !important;
        }

        #chatbotButton.toggled {
            padding: 0 !important;
            height: 60px !important;
            width: 60px !important;
        }

        #chatbotButton:hover {
            transform: scale(1.1) !important;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3) !important;
        }

        #chatbotButton:active {
            transform: scale(0.95) !important;
        }

        #chatContainer {
            background-color: white !important;
            position: fixed !important;
            bottom: -500px !important;
            right: 20px !important;
            width: 450px !important;
            height: 500px !important;
            border-radius: 10px !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2) !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
            z-index: 999 !important;
            opacity: 0 !important;
            transition: bottom 0.5s ease, opacity 0.5s ease !important;
        }

        #chatContainer.visible {
            bottom: 100px !important;
            opacity: 1 !important;
        }

        #chatHeader {
            background: linear-gradient(45deg, #ff5a00, #ff9c40) !important;
            color: #fff !important;
            padding: 1px 15px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
        }

        #chatHeader img {
            width: 30px !important;
            height: 30px !important;
            margin-right: 10px !important;
        }

        #chatHeader h4 {
            margin: 0 !important;
            font-size: 16px !important;
            font-weight: normal !important;
        }

        #closeChat {
            background: none !important;
            border: none !important;
            color: #fff !important;
            cursor: pointer !important;
            font-size: 18px !important;
            margin-left: auto !important;
        }

        #chatBody {
            flex: 1 !important;
            padding: 10px !important;
            overflow-y: auto !important;
            background: #f9f9f9 !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 10px !important;
        }

        .button-container {
            width: 100% !important;
            display: block !important;
            text-align: left !important;
            margin-top: 5px !important;
        }

        .bot-message {
            font-size: 14px !important;
            background: transparent !important;
            color: #333 !important;
            padding: 10px 15px !important;
            border-radius: 15px 15px 15px 0 !important;
            border: 1px solid #ff5a00 !important;
            max-width: 75% !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
            word-wrap: break-word !important;
        }

        .bot-message-container {
            display: flex !important;
            align-items: flex-end !important;
            margin-bottom: 10px !important;
        }

        .bot-message-container img {
            background-color: #ff5a00 !important;
            width: 30px !important;
            height: 30px !important;
            border-radius: 50% !important;
            margin-right: 10px !important;
        }

        .bot-message:after {
            content: "" !important;
            position: absolute !important;
            top: 12px !important;
            left: -6px !important;
            width: 0 !important;
            height: 0 !important;
            border-style: solid !important;
            border-color: transparent #ebedee transparent transparent !important;
        }

        .user-message {
            background: linear-gradient(135deg, #ff5a00, #ff9c40) !important;
            color: white !important;
            padding: 10px 15px !important;
            border-radius: 15px 15px 0 15px !important;
            align-self: flex-end !important;
            max-width: 75% !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
            word-wrap: break-word !important;
            position: relative !important;
            margin-bottom: 10px !important;
        }

        .user-message-container {
            display: flex !important;
            justify-content: flex-end !important;
            margin-bottom: 10px !important;
        }

        .user-message-container .timestamp {
            color: rgb(27, 26, 26) !important;
        }

        .user-message:after {
            content: "" !important;
            position: absolute !important;
            top: 12px !important;
            right: -6px !important;
            width: 0 !important;
            height: 0 !important;
            border-style: solid !important;
            border-color: transparent transparent transparent #ff9c40 !important;
        }

        .timestamp {
            font-size: 10px !important;
            margin-top: 5px !important;
            text-align: right !important;
            color: #777 !important;
        }

        #chatFooter {
            display: none;
            margin: 10px !important;
            align-items: center !important;
            padding: 0 !important;
            border: 1px solid !important;
            border-image: linear-gradient(45deg, #ff5a00, #ff9c40) 1 !important;
            background: white !important;
            border-bottom-left-radius: 10px !important;
            border-bottom-right-radius: 10px !important;
        }

        #chatInput {
            flex: 1 !important;
            padding: 1rem 5.625rem 12px 1rem !important;
            border: none !important;
            outline: none !important;
            font-size: 14px !important;
        }

        #sendMessage {
            background: linear-gradient(45deg, #ff5a00, #ff9c40) !important;
            color: white !important;
            border: none !important;
            padding: 8px 12px !important;
            margin: 4px !important;
            border-radius: 50% !important;
            height: 40px !important;
            width: 40px !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 16px !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2) !important;
        }

        #sendMessage:hover {
            transform: scale(1.1) !important;
        }

        button.option-btn {
            display: inline-block !important;
            text-align: left !important;
            margin: 2px !important;
            background: transparent !important;
            color: #333 !important;
            border: 1px solid #ff5a00 !important;
            padding: 8px 10px !important;
            border-radius: 20px !important;
            cursor: pointer !important;
            font-size: 14px !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
            transition: background 0.3s ease, transform 0.3s ease !important;
        }

        button.option-btn:hover {
            background: linear-gradient(135deg, #ff9c40, #ff5a00) !important;
            transform: scale(1.05) !important;
        }

        #contactForm {
            display: flex !important;
            flex-direction: column !important;
            gap: 15px !important;
            padding: 20px !important;
            background: #fff !important;
            border-radius: 10px !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
        }

        #contactForm .form-group {
            display: flex !important;
            flex-direction: column !important;
            gap: 5px !important;
        }

        #contactForm label {
            font-size: 14px !important;
            color: #555 !important;
        }

        #contactForm input,
        #contactForm textarea {
            padding: 10px !important;
            font-size: 14px !important;
            border: 1px solid #ddd !important;
            border-radius: 5px !important;
            transition: border-color 0.3s ease !important;
        }

        #contactForm input:focus,
        #contactForm textarea:focus {
            border-color: #ff5a00 !important;
            outline: none !important;
        }

        #contactForm textarea {
            resize: none !important;
            height: 80px !important;
        }

        #userForm,
        #existingUserFormID {
            display: flex !important;
            flex-direction: column !important;
            gap: 15px !important;
            padding: 20px !important;
            background: #fff !important;
            border-radius: 10px !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
        }

        #userForm .form-group,
        #existingUserFormID .form-group {
            display: flex !important;
            flex-direction: column !important;
            gap: 5px !important;
        }

        #userForm label,
        #existingUserFormID label {
            font-size: 14px !important;
            color: #555 !important;
        }

        #userForm input,
        #userForm textarea,
        #existingUserFormID input,
        #existingUserFormID textarea {
            padding: 10px !important;
            font-size: 14px !important;
            border: 1px solid #ddd !important;
            border-radius: 5px !important;
            transition: border-color 0.3s ease !important;
        }

        #userForm input:focus,
        #userForm textarea:focus,
        #existingUserFormID input:focus,
        #existingUserFormID textarea:focus {
            border-color: #ff5a00 !important;
            outline: none !important;
        }

        #userForm textarea,
        #existingUserFormID textarea {
            resize: none !important;
            height: 80px !important;
        }

        .form-submit-btn {
            padding: 10px 15px !important;
            font-size: 14px !important;
            font-weight: bold !important;
            color: #fff !important;
            background: linear-gradient(45deg, #ff5a00, #ff9c40) !important;
            border: none !important;
            border-radius: 25px !important;
            cursor: pointer !important;
            transition: background 0.3s ease, transform 0.3s ease !important;
        }

        .form-submit-btn:hover {
            background: linear-gradient(45deg, #ff9c40, #ff5a00) !important;
            transform: scale(1.05) !important;
        }

        .form-submit-btn:active {
            transform: scale(0.95) !important;
        }

        .typing {
            display: flex !important;
            justify-content: space-around !important;
            width: 40px !important;
        }

        .typing span {
            display: inline-block !important;
            width: 8px !important;
            height: 8px !important;
            background: #ff5a00 !important;
            border-radius: 50% !important;
            animation: blink 1.4s infinite ease-in-out both !important;
        }

        .typing span:nth-child(1) {
            animation-delay: -0.32s !important;
        }

        .typing span:nth-child(2) {
            animation-delay: -0.16s !important;
        }

        .typing span:nth-child(3) {
            animation-delay: 0 !important;
        }

        @keyframes blink {

            0%,
            80%,
            100% {
                transform: scale(0);
            }

            40% {
                transform: scale(1);
            }
        }

        @media (max-width: 768px) {
            #chatContainer {
                width: 100% !important;
                height: 80% !important;
                bottom: 0 !important;
                right: 0 !important;
                border-radius: 0 !important;
            }

            #chatHeader {
                padding: 10px !important;
                font-size: 14px !important;
            }

            #chatHeader h4 {
                font-size: 14px !important;
            }

            #chatBody {
                padding: 8px !important;
            }

            #sendMessage {
                padding: 10px !important;
                font-size: 18px !important;
            }

            .bot-message,
            .user-message {
                font-size: 12px !important;
                padding: 8px 12px !important;
                max-width: 90% !important;
            }

            .timestamp {
                font-size: 8px !important;
            }

            .option-btn {
                font-size: 12px !important;
                padding: 6px 8px !important;
            }
        }
    </style>

    <!-- Chatbot JavaScript -->
    <script>
        const chatbotButton = document.getElementById("chatbotButton");
        const chatContainer = document.getElementById("chatContainer");
        const closeChat = document.getElementById("closeChat");
        const chatBody = document.getElementById("chatBody");
        const chatInput = document.getElementById("chatInput");
        const sendMessageButton = document.getElementById("sendMessage");
        const API_URL = "https://chatbot.nipige.com/webhooks/rest/webhook";
        let senderId = null;
        let isChatLoaded = false;

        function generateSenderId(name, email) {
            return (
                name.replace(/\s+/g, "").toLowerCase() +
                email.split("@")[0].toLowerCase()
            );
        }

        chatbotButton.addEventListener("click", async () => {
            const userData = JSON.parse(localStorage.getItem("chatUserData"));
            if (!userData) {
                document.getElementById("chatContainer").classList.remove("hidden");
                $("#userForm").show();
                $("#existingUserButton").show();
            } else {
                senderId = generateSenderId(userData.name, userData.email);
                document.getElementById("userFormContainer").classList.add("hidden");
                document.getElementById("chatFooter").classList.remove("hidden");
                document.getElementById("chatContainer").classList.remove("hidden");
            }
        });

        $(document).on("submit", "#userForm", async (event) => {
            event.preventDefault();
            const name = document.getElementById("name").value.trim();
            const email = document.getElementById("email").value.trim();
            const company = document.getElementById("company").value.trim();
            const jobTitle = document.getElementById("jobTitle").value.trim();
            senderId = generateSenderId(name, email);
            const userData = { name, email, company, jobTitle, senderId };
            localStorage.setItem("chatUserData", JSON.stringify(userData));
            document.cookie = `chatUserData=${JSON.stringify(
            userData
        )}; path=/; max-age=${7 * 24 * 60 * 60}`;
            document.getElementById("userFormContainer").classList.add("hidden");
            document.getElementById("chatFooter").style.display = "flex";
            const payload = {
                sender: senderId,
                metadata: { type: "trigital_chat_option" },
                message: "welcome trigital chat",
            };
            await sendPayload(payload);
            $("#userForm").hide();
            $("#existingUserButton").hide();
        });

        document
            .getElementById("existingUserButton")
            .addEventListener("click", function () {
                const existingUserForm = document.createElement("form");
                existingUserForm.id = "existingUserFormID";
                existingUserForm.innerHTML = `
    <div class="form-group">
      <label for="existingName"><b>Name:</b></label>
      <input type="text" id="existingName" required />
    </div>
    <div class="form-group">
      <label for="existingEmail"><b>Email:</b></label>
      <input type="email" id="existingEmail" required />
    </div>
    <button class="form-submit-btn" type="submit">Submit</button>
  `;
                $(document).on("submit", "#existingUserFormID", async (event) => {
                    event.preventDefault();
                    const existingName = document
                        .getElementById("existingName")
                        .value.trim();
                    const existingEmail = document
                        .getElementById("existingEmail")
                        .value.trim();
                    senderId = generateSenderId(existingName, existingEmail);
                    const storedData = JSON.parse(localStorage.getItem("chatUserData"));
                    if (storedData && storedData.senderId === senderId) {
                        document
                            .getElementById("userFormContainer")
                            .classList.add("hidden");
                        document.getElementById("chatFooter").style.display = "flex";
                        appendMessage("Bot", `Welcome back, ${existingName}!`);
                        if (isChatLoaded) {
                            const payload = {
                                sender: senderId,
                                metadata: { type: "trigital_chat_option" },
                                message: "welcome trigital chat",
                            };
                            $("#existingUserFormID").hide();
                            isChatLoaded = false;
                            await sendPayload(payload);
                        }
                    } else {
                        const response = await fetch(
                            `https://chatbot.nipige.com/conversations/${senderId}/tracker`
                        );
                        const data = await response.json();
                        if (!data.latest_message.text) {
                            $("#userFormContainer").html(`<form id="userForm">
          <label for="name"><b>Name:</b></label>
          <input type="text" id="name" required />
          <label for="email"><b>Email:</b></label>
          <input type="email" id="email" required />
          <label for="company"><b>Company:</b></label>
          <input type="text" id="company" required />
          <label for="jobTitle"><b>Job Title:</b></label>
          <input type="text" id="jobTitle" required />
          <button class="form-submit-btn" type="submit">Submit</button>
        </form>`);
                            appendMessage(
                                "Bot",
                                "Sorry, we couldn't find any information about you. Please share your details again."
                            );
                            // alert(
                            //   "Sorry, we couldn't find any information about you. Please share your details again."
                            // );
                            $("#chatFooter").addClass("hidden");
                            document
                                .getElementById("userFormContainer")
                                .classList.remove("hidden");
                        } else {
                            $("#existingUserFormID").hide();
                            appendMessage("Bot", `Welcome back, ${existingName}!`);
                            if (isChatLoaded) {
                                const payload = {
                                    sender: senderId,
                                    metadata: { type: "trigital_chat_option" },
                                    message: "welcome trigital chat",
                                };
                                $("#existingUserFormID").hide();
                                isChatLoaded = false;
                                await sendPayload(payload);
                            }
                            document
                                .getElementById("userFormContainer")
                                .classList.add("hidden");
                            document.getElementById("chatFooter").style.display = "flex";
                        }
                    }
                });
                document.getElementById("userFormContainer").innerHTML = "";
                document
                    .getElementById("userFormContainer")
                    .appendChild(existingUserForm);
            });

        function setCookie(name, value, days) {
            const date = new Date();
            date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
            const expires = "expires=" + date.toUTCString();
            document.cookie =
                name + "=" + JSON.stringify(value) + ";" + expires + ";path=/";
        }

        function getCookie(name) {
            const cookieName = name + "=";
            const decodedCookie = decodeURIComponent(document.cookie);
            const cookieArray = decodedCookie.split(";");
            for (let i = 0; i < cookieArray.length; i++) {
                let cookie = cookieArray[i].trim();
                if (cookie.indexOf(cookieName) === 0) {
                    return JSON.parse(cookie.substring(cookieName.length));
                }
            }
            return null;
        }

        chatbotButton.addEventListener("click", async () => {
            if (chatContainer.classList.contains("visible")) {
                chatContainer.classList.remove("visible");
                chatbotButton.innerHTML =
                    '<img id="chatbotIcon" src="https://trigitaltech.com/wp-content/uploads/2024/12/chatbot-icon.svg" alt="chatbot-icon" />';
                chatbotButton.setAttribute("aria-label", "Open Chatbot");
                isChatOpen = false;
            } else {
                chatContainer.classList.add("visible");
                chatbotButton.innerHTML = '<i class="fas fa-times"></i>';
                chatbotButton.setAttribute("aria-label", "Close Chatbot");
                isChatOpen = true;
                if (!isChatLoaded) {
                    const payload = {
                        sender: senderId,
                        metadata: { type: "trigital_chat_option" },
                        message: "welcome trigital chat",
                    };
                    isChatLoaded = true;
                }
            }
        });

        closeChat.addEventListener("click", () => {
            chatContainer.classList.remove("visible");
            chatbotButton.innerHTML =
                '<img id="chatbotIcon" src="https://trigitaltech.com/wp-content/uploads/2024/12/chatbot-icon.svg" alt="chatbot-icon" />';
            chatbotButton.setAttribute("aria-label", "Open Chatbot");
            isChatOpen = false;
        });

        sendMessageButton.addEventListener("click", sendMessage);
        chatInput.addEventListener("keydown", (event) => {
            if (event.key === "Enter") {
                event.preventDefault();
                sendMessage();
            }
        });

        async function sendMessage() {
            const message = chatInput.value.trim();
            if (message) {
                const payload = {
                    sender: senderId,
                    metadata: { type: "trigital_chat_vector_search" },
                    message: message,
                };
                appendMessage("You", message);
                chatInput.value = "";
                showTypingIndicator();
                await sendPayload(payload);
                removeTypingIndicator();
            }
        }

        async function sendPayload(payload) {
            try {
                const response = await fetch(API_URL, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload),
                });
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }

                const data = await response.json();
                handleResponse(data);
            } catch (error) {
                console.error("Error sending message:", error);
                appendMessage(
                    "Bot",
                    "There was an issue processing your request. Please try again."
                );
            }
        }

        function handleResponse(data) {
            removeTypingIndicator();
            data.forEach((message) => {
                if (message.text) {
                    appendMessage("Bot", message.text);
                }
                if (message.buttons) {
                    appendButtons(message.buttons);
                }
            });
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function appendMessage(sender, message, saveToCookie = true) {
            const container = document.createElement("div");
            if (sender === "Bot") {
                container.className = "bot-message-container";
                const botImg = document.createElement("img");
                botImg.src =
                    "https://trigitaltech.com/wp-content/uploads/2024/12/chatbot-icon.svg";
                botImg.className = "bot-profile-img";
                container.appendChild(botImg);
            } else {
                container.className = "user-message-container";
            }
            const bubble = document.createElement("div");
            if (sender === "Bot") {
                bubble.className = "bot-message";
            } else {
                bubble.className = "user-message";
            }
            const regex = /\[([^\]]+)\]\((https?:\/\/[^\s]+)\)/g;
            let match;
            let lastIndex = 0;
            while ((match = regex.exec(message)) !== null) {
                if (match.index > lastIndex) {
                    const textNode = document.createTextNode(
                        message.slice(lastIndex, match.index)
                    );
                    bubble.appendChild(textNode);
                }
                const buttonContainer = document.createElement("div");
                buttonContainer.className = "button-container";
                if (match[1] && match[2]) {
                    const button = document.createElement("button");
                    button.className = "option-btn";
                    button.textContent = match[1];
                    const url = match[2];
                    button.onclick = () => {
                        if (url) {
                            window.open(url, "_blank");
                        } else {
                            console.error("Invalid URL:", url);
                        }
                    };
                    buttonContainer.appendChild(button);
                    bubble.appendChild(buttonContainer);
                } else {
                    console.error("Regex match is incomplete:", match);
                }
                lastIndex = regex.lastIndex;
            }
            if (lastIndex < message.length) {
                const textNode = document.createTextNode(message.slice(lastIndex));
                bubble.appendChild(textNode);
            }
            const timestamp = document.createElement("div");
            timestamp.className = "timestamp";
            timestamp.innerText = new Date().toLocaleTimeString([], {
                hour: "2-digit",
                minute: "2-digit",
            });
            bubble.appendChild(timestamp);
            container.appendChild(bubble);
            chatBody.appendChild(container);
            chatBody.scrollTop = chatBody.scrollHeight;
            if (saveToCookie) {
                const chatHistory = getCookie("chatHistory") || [];
                chatHistory.push({ sender, text: message });
                setCookie("chatHistory", chatHistory, 7); // Save for 7 days
            }
        }

        function appendButtons(buttons) {
            const buttonContainer = document.createElement("div");
            buttonContainer.className = "button-container";
            buttons.forEach((button) => {
                const btn = document.createElement("button");
                btn.textContent = button.title;
                btn.className = "option-btn";
                btn.addEventListener("click", async () => {
                    showTypingIndicator();
                    if (button.title === "Contact Us") {
                        showContactForm();
                    } else {
                        const payloadData = {
                            sender: senderId,
                            metadata: { type: "trigital_chat_option" },
                            message: button.payload,
                        };
                        appendMessage("You", button.title);
                        await sendPayload(payloadData);
                    }
                    removeTypingIndicator();
                });
                buttonContainer.appendChild(btn);
            });
            chatBody.appendChild(buttonContainer);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function showContactForm() {
            const form = document.createElement("form");
            form.id = "contactForm";
            form.innerHTML = `
    <div class="form-group">
      <label for="name">Name*</label>
      <input type="text" id="name" name="name" required placeholder="Enter Your Name">
    </div>
    <div class="form-group">
      <label for="email">Business Email*</label>
      <input type="email" id="email" name="email" required placeholder="Your Email">
    </div>
    <div class="form-group">
      <label for="phone">Phone Number*</label>
      <input type="tel" id="phone" name="phone" required placeholder="Your Phone">
    </div>
    <div class="form-group">
      <label for="topic">Topic</label>
      <input type="text" id="topic" name="topic" placeholder="Topic">
    </div>
    <div class="form-group">
      <label for="message">Message*</label>
      <textarea id="message" name="message" required placeholder="Your Message"></textarea>
    </div>
    <button type="submit" class="form-submit-btn">Submit</button>
  `;
            form.addEventListener("submit", async (event) => {
                event.preventDefault();
                const formData = {
                    name: form.name.value.trim(),
                    email: form.email.value.trim(),
                    phone: form.phone.value.trim(),
                    topic: form.topic.value.trim(),
                    message: form.message.value.trim(),
                };
                if (
                    !formData.name ||
                    !formData.email ||
                    !formData.phone ||
                    !formData.message
                ) {
                    alert("Please fill in all required fields.");
                    return;
                }
                const payload = {
                    sender: senderId,
                    metadata: {
                        type: "trigital_chat_option",
                        name: form.name.value,
                        email: form.email.value,
                        message: form.message.value,
                        phone: form.phone.value,
                        topic: form.topic.value,
                    },
                    message: "trigital_contact_us_submit",
                };

                showTypingIndicator();
                await sendPayload(payload);
                chatBody.scrollTop = chatBody.scrollHeight;
                form.name.value = "";
                form.email.value = "";
                form.message.value = "";
                form.phone.value = "";
                form.topic.value = "";
                removeTypingIndicator();
            });
            chatBody.appendChild(form);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        document
            .getElementById("chatbotButton")
            .addEventListener("click", function () {
                const button = this;
                button.classList.toggle("toggled");
            });

        function showTypingIndicator() {
            const typingIndicator = document.createElement("div");
            typingIndicator.id = "typingIndicator";
            typingIndicator.className = "bot-message-container";
            typingIndicator.innerHTML = `
    <div class="bot-message typing">
      <span></span><span></span><span></span>
    </div>`;
            chatBody.appendChild(typingIndicator);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function removeTypingIndicator() {
            const typingIndicator = document.getElementById("typingIndicator");
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }
    </script>
    <?php
}
add_action('wp_footer', 'add_chatbot_to_footer');