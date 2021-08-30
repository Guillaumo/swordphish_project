"use strict";
const render_block = document.getElementById('render_block');
const input_pwd = document.getElementById("pwd_update");
input_pwd.style.display = "none";
render_block.addEventListener("click", () => {
    if(getComputedStyle(input_pwd).display != "block") {
        input_pwd.style.display = "block";
    }
    else {
        input_pwd.style.display = "none";
    }
    
})