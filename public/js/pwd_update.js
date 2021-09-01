"use strict";
const eye = document.querySelector(".fa-eye");
const eyeoff = document.querySelector(".fa-eye-slash");
const passwordField1 = document.querySelector(".pwd1");
const passwordField2 = document.querySelector(".pwd2");

eye.addEventListener("click", () => {
    eye.style.display = "none";
    eyeoff.style.display = "block";
    passwordField1.type = "text";
  });
  
  eyeoff.addEventListener("click", () => {
    eyeoff.style.display = "none";
    eye.style.display = "block";
    passwordField1.type = "password";
  });

  passwordField1.addEventListener('input',() => {
    passwordField2.setAttribute("required","");
  });

  passwordField2.addEventListener('input',() => {
    passwordField1.setAttribute("required","");
  });

  let check = function() {
    if (passwordField1.value == passwordField2.value) {
      document.getElementById('message').style.color = 'green';
      document.getElementById('message').innerHTML = 'Correspond';
      document.getElementById('submit_update').disabled = false;
    }
    else {
      document.getElementById('message').style.color = 'red';
      document.getElementById('message').innerHTML = 'Ne correspond pas';
      document.getElementById('submit_update').disabled = "true";
    }
  }