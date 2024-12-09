const chatbotButton = document.getElementById("chatbotButton");
const chatContainer = document.getElementById("chatContainer");
const closeChat = document.getElementById("closeChat");
const chatBody = document.getElementById("chatBody");
const chatInput = document.getElementById("chatInput");
const sendMessageButton = document.getElementById("sendMessage");

const API_URL = "https://chatbot.nipige.com/webhooks/rest/webhook";
const senderId = "671f66d3ca5fec457479955a";// should be unique

let isChatLoaded = false; // Flag to check if the initial chat is loaded

// Show chat container and load initial message
chatbotButton.addEventListener("click", async () => {
  chatContainer.classList.add("visible");
  if (!isChatLoaded) {
    const payload = {
      sender: senderId,
      metadata: { type: "trigital_chat_option" },
      message: "welcome trigital chat",
    };
    await sendPayload(payload);
    isChatLoaded = true;
  }
});

// Hide chat container
closeChat.addEventListener("click", () => {
  chatContainer.classList.remove("visible");
});

// Handle user input for free text when clicking the Send button
sendMessageButton.addEventListener("click", sendMessage);

// Handle user input for free text when pressing Enter key
chatInput.addEventListener("keydown", (event) => {
  if (event.key === "Enter") {
    event.preventDefault(); // Prevent newline insertion
    sendMessage();
  }
});

// Function to handle message sending
async function sendMessage() {
  const message = chatInput.value.trim();

  if (message) {
    const payload = {
      sender: senderId,
      metadata: { type: "trigital" },
      message: message,
    };

    appendMessage("You", message); // Show the user's message in the chat
    await sendPayload(payload);
    chatInput.value = ""; // Clear input field
  }
}

// Function to send payload to the API
async function sendPayload(payload) {
  try {
    const response = await fetch(API_URL, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });

    const data = await response.json();
    handleResponse(data);
  } catch (error) {
    console.error("Error sending message:", error);
  }
}

// Append messages or options to the chat window
function handleResponse(data) {
  data.forEach((message) => {
    if (message.text) {
      appendMessage("Bot", message.text);
    }

    if (message.buttons) {
      message.buttons.forEach((button) => {
        appendButton(button.title, button.payload);
      });
    }
  });
}

// Add message to the chat
function appendMessage(sender, message) {
  const bubble = document.createElement("div");
  bubble.className = sender === "You" ? "user-message" : "bot-message";
  bubble.textContent = message;

  chatBody.appendChild(bubble);
  chatBody.scrollTop = chatBody.scrollHeight; // Auto-scroll to the latest message
}

// Add buttons to the chat
function appendButton(title, payload) {
  const button = document.createElement("button");
  button.textContent = title;
  button.className = "option-btn";
  button.addEventListener("click", async () => {
    const payloadData = {
      sender: senderId,
      metadata: { type: "trigital_chat_option" },
      message: payload,
    };

    appendMessage("You", title); // Show the button selection as a message
    await sendPayload(payloadData);
  });
  chatBody.appendChild(button);
}
