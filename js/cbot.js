function toggleChat() {
    let chatPopup = document.getElementById("chat-popup");
    chatPopup.style.display = chatPopup.style.display === "none" || chatPopup.style.display === "" ? "flex" : "none";
}

function sendMessage() {
    let inputField = document.getElementById("user-input");
    let message = inputField.value.trim();
    if (message === "") return;
    
    let chatBody = document.getElementById("chat-body");
    let userMessage = document.createElement("div");
    userMessage.className = "chat-message user-message";
    userMessage.textContent = message;
    chatBody.appendChild(userMessage);
    
    inputField.value = "";
    chatBody.scrollTop = chatBody.scrollHeight;
    
    setTimeout(() => {
        let botMessage = document.createElement("div");
        botMessage.className = "chat-message bot-message";
        botMessage.textContent = getBotResponse(message);
        chatBody.appendChild(botMessage);
        chatBody.scrollTop = chatBody.scrollHeight;
    }, 1000);
}

function getBotResponse(textarea) {
    let responses = {
        "hi": "Hello! How can I assist you?",
        "how are you": "I'm just a bot, but I'm doing great! 😊",
        "tell me a joke": "Why don’t skeletons fight each other? Because they don’t have the guts! 😆",
        "bye": "Goodbye! Have a great day!"
    };
    return responses[textarea.toLowerCase()] || "Sorry, I don't understand that.";
}

function resizetextarea(textarea) {
    textarea.style.width = textarea.value.length + "ch"; // Adjust width based on character count
  }