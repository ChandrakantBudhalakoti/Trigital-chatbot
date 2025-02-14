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
    name.replace(/\s+/g, "").toLowerCase() + email.split("@")[0].toLowerCase()
  );
}

function setCookie(name, value, days) {
  const date = new Date();
  date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
  const expires = "expires=" + date.toUTCString();
  document.cookie =
    name + "=" + JSON.stringify(value) + ";" + expires + ";path=/";
}

function setSessionCookie(name, value, minutes) {
  const date = new Date();
  date.setTime(date.getTime() + minutes * 60 * 1000);
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

function appendMessage(sender, message, saveToCookie = true) {
  const container = document.createElement("div");
  container.className =
    sender === "Bot" ? "bot-message-container" : "user-message-container";

  if (sender === "Bot") {
    const botImg = document.createElement("img");
    botImg.src =
      "https://trigitaltech.com/wp-content/uploads/2024/12/chatbot-icon.svg";
    botImg.className = "bot-profile-img";
    container.appendChild(botImg);
  }

  const bubble = document.createElement("div");
  bubble.className = sender === "Bot" ? "bot-message" : "user-message";

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

document.getElementById("chatbotButton").addEventListener("click", function () {
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
    setSessionCookie("chatSession", { senderId }, 30); // Set session for 30 minutes
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

chatbotButton.addEventListener("click", async () => {
  const sessionData = getCookie("chatSession");
  if (sessionData) {
    senderId = sessionData.senderId;
    const chatHistory = getCookie("chatHistory") || [];
    chatHistory.forEach((message) => {
      appendMessage(message.sender, message.text, false);
    });
    document.getElementById("chatContainer").classList.remove("hidden");
    document.getElementById("chatFooter").style.display = "flex";
  } else {
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
      const existingName = document.getElementById("existingName").value.trim();
      const existingEmail = document
        .getElementById("existingEmail")
        .value.trim();
      senderId = generateSenderId(existingName, existingEmail);
      const storedData = JSON.parse(localStorage.getItem("chatUserData"));
      if (storedData && storedData.senderId === senderId) {
        document.getElementById("userFormContainer").classList.add("hidden");
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
          $("#userFormContainer").html(`
          <form id="userForm">
            <label for="name"><b>Name:</b></label>
            <input type="text" id="name" required />
            <label for="email"><b>Email:</b></label>
            <input type="email" id="email" required />
            <label for="company"><b>Company:</b></label>
            <input type="text" id="company" required />
            <label for="jobTitle"><b>Job Title:</b></label>
            <input type="text" id="jobTitle" required />
            <button class="form-submit-btn" type="submit">Submit</button>
          </form>
        `);
          appendMessage(
            "Bot",
            "Sorry, we couldn't find any information about you. Please share your details again."
          );
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
          document.getElementById("userFormContainer").classList.add("hidden");
          document.getElementById("chatFooter").style.display = "flex";
        }
      }
    });
    document.getElementById("userFormContainer").innerHTML = "";
    document.getElementById("userFormContainer").appendChild(existingUserForm);
  });

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
