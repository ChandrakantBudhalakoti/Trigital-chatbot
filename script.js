const chatbotButton = document.getElementById("chatbotButton");
const chatContainer = document.getElementById("chatContainer");
const closeChat = document.getElementById("closeChat");
const chatBody = document.getElementById("chatBody");
const chatInput = document.getElementById("chatInput");
const sendMessageButton = document.getElementById("sendMessage");

const API_URL = "https://chatbot.nipige.com/webhooks/rest/webhook";
const senderId =
  "671f66d3ca5fec457479955a" + Math.floor(Math.random() * 10000).toString();

let isChatLoaded = false;

chatbotButton.addEventListener("click", async () => {
  // Toggle visibility
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

    // Load chatbot only once
    if (!isChatLoaded) {
      const payload = {
        sender: senderId,
        metadata: { type: "trigital_chat_option" },
        message: "welcome trigital chat",
      };
      await sendPayload(payload);
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

function sendMessage() {
  const message = chatInput.value.trim();
  if (message) {
    appendMessage("You", message);
    chatInput.value = "";
  }
}

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
      metadata: { type: "trigital" },
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

    const data = await response.json();
    handleResponse(data);
  } catch (error) {
    console.error("Error sending message:", error);
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

function appendMessage(sender, message) {
  const container = document.createElement("div");

  // Check sender and apply corresponding container class
  if (sender === "Bot") {
    container.className = "bot-message-container"; // Container for bot message

    // Add bot profile image
    const botImg = document.createElement("img");
    botImg.src =
      "https://trigitaltech.com/wp-content/uploads/2024/12/chatbot-icon.svg";
    botImg.className = "bot-profile-img";
    container.appendChild(botImg);
  } else {
    container.className = "user-message-container";
  }

  // Create the message bubble
  const bubble = document.createElement("div");

  if (sender === "Bot") {
    bubble.className = "bot-message"; // Class for bot message bubble
  } else {
    bubble.className = "user-message"; // Class for user message bubble
  }

  // Parse message for [text](link) format
  const regex = /\[([^\]]+)\]\((https?:\/\/[^\s]+)\)/g;
  let match;
  let lastIndex = 0;

  while ((match = regex.exec(message)) !== null) {
    // Append any text before the match
    if (match.index > lastIndex) {
      const textNode = document.createTextNode(
        message.slice(lastIndex, match.index)
      );
      bubble.appendChild(textNode);
    }

    // Create a container for the button (to force the button on a new line)
    const buttonContainer = document.createElement("div");
    buttonContainer.className = "button-container"; // Optionally, you can add a class for styling

    // Create button for the matched text
    if (match[1] && match[2]) {
      const button = document.createElement("button");
      button.className = "option-btn";
      button.textContent = match[1]; // Button text from []
      const url = match[2]; // Capture the URL in a local variable
      button.onclick = () => {
        if (url) {
          window.open(url, "_blank"); // Open link in a new tab
        } else {
          console.error("Invalid URL:", url);
        }
      };
      buttonContainer.appendChild(button); // Add the button to the container
      bubble.appendChild(buttonContainer); // Add the container (with the button) to the message bubble
    } else {
      console.error("Regex match is incomplete:", match);
    }
    lastIndex = regex.lastIndex;
  }

  // Append any remaining text after the last match
  if (lastIndex < message.length) {
    const textNode = document.createTextNode(message.slice(lastIndex));
    bubble.appendChild(textNode);
  }

  // Add timestamp
  const timestamp = document.createElement("div");
  timestamp.className = "timestamp";
  timestamp.innerText = new Date().toLocaleTimeString([], {
    hour: "2-digit",
    minute: "2-digit",
  });
  bubble.appendChild(timestamp);

  // Append the message bubble to the container
  container.appendChild(bubble);

  // Append the container to the chat body
  chatBody.appendChild(container);

  // Scroll to the bottom
  chatBody.scrollTop = chatBody.scrollHeight;
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
        showContactForm(); // Show the contact form
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
  // Create the form
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

  // Add submit event listener
  form.addEventListener("submit", async (event) => {
    event.preventDefault();

    // Gather form data
    const formData = {
      name: form.name.value.trim(),
      email: form.email.value.trim(),
      phone: form.phone.value.trim(),
      topic: form.topic.value.trim(),
      message: form.message.value.trim(),
    };

    // Basic validation
    if (
      !formData.name ||
      !formData.email ||
      !formData.phone ||
      !formData.message
    ) {
      alert("Please fill in all required fields.");
      return;
    }

    // Create payload
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
    // Send payload
    await sendPayload(payload);
    chatBody.scrollTop = chatBody.scrollHeight;
    form.name.value = "",
    form.email.value = "",
    form.message.value = "",
    form.phone.value = "",
    form.topic.value = "",
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
