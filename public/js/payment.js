const fileInput = document.getElementById("payment-proof");
const fileBtn = document.getElementById("Upload-btn");

fileInput.addEventListener("change", function () {
    const file = this.files[0];

    if (file) {
        fileBtn.innerText = file.name;
        fileBtn.classList.add("font-semibold");
    } else {
        fileBtn.innerText = "Upload file";
        fileBtn.classList.remove("font-semibold");
    }
});
