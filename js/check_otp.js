function moveToNext(input) {
    if (input.value.length === 1) {
        let nextInput = input.nextElementSibling;
        if (nextInput) {
            nextInput.focus();
        }
    }
}

function moveToPrev(event, input) {
    if (event.key === "Backspace" && input.value === "") {
        let prevInput = input.previousElementSibling;
        if (prevInput) {
            prevInput.focus();
        }
    }
}