document.addEventListener("DOMContentLoaded", function () {
    const navbar = document.querySelector(".navbar");

    window.addEventListener("scroll", function () {
        if (window.scrollY > 50) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }
    });
});


document.addEventListener("DOMContentLoaded", function () {
    const themeToggle = document.getElementById("theme-toggle");
    const body = document.body;

    // Check local storage for theme preference
    if (localStorage.getItem("theme") === "dark") {
        body.classList.add("dark-mode");
        themeToggle.textContent = "☀️ Light Mode";
    }

    themeToggle.addEventListener("click", function () {
        body.classList.toggle("dark-mode");
        if (body.classList.contains("dark-mode")) {
            localStorage.setItem("theme", "dark");
            themeToggle.textContent = "☀️ Light Mode";
        } else {
            localStorage.setItem("theme", "light");
            themeToggle.textContent = "🌙 Dark Mode";
        }
    });
});



document.addEventListener("DOMContentLoaded", function () {
    const serviceFilter = document.getElementById("serviceFilter");
    const serviceList = document.getElementById("serviceList");

    serviceFilter.addEventListener("change", function () {
        const filterValue = serviceFilter.value.toLowerCase();
        const services = serviceList.querySelectorAll(".col-md-4");

        services.forEach(function (service) {
            const category = service.getAttribute("data-category").toLowerCase();
            if (filterValue === "" || category.includes(filterValue)) {
                service.style.display = "block";  // Show matching service
            } else {
                service.style.display = "none";  // Hide non-matching service
            }
        });
    });
});



document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("contactForm");
    const validationAlert = document.getElementById("validationAlert");

    form.addEventListener("submit", function (event) {
        event.preventDefault(); 

  
        validationAlert.classList.add("d-none");

        // Get form field values
        const email = document.getElementById("email").value;
        const name = document.getElementById("name").value;
        const reason = document.getElementById("reason").value;


        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Check for empty fields
        if (!email || !name || !reason) {
            validationAlert.textContent = "All fields are required.";
            validationAlert.classList.remove("d-none");
            return; 
        }


        if (!emailRegex.test(email)) {
            validationAlert.textContent = "Please enter a valid email address.";
            validationAlert.classList.remove("d-none");
            return;
        }


        if (reason.length < 10) {
            validationAlert.textContent = "Reason for contact must be at least 10 characters long.";
            validationAlert.classList.remove("d-none");
            return;
        }


        validationAlert.classList.add("d-none");
        alert("Form submitted successfully!");

   
        form.reset();
    });
});



document.addEventListener("DOMContentLoaded", function () {

    const currentYear = new Date().getFullYear();
    document.getElementById("currentYear").textContent = currentYear;


    const footer = document.getElementById("footer");


    footer.addEventListener("mouseover", function () {
        footer.style.backgroundColor = "#121212"; 
    });

    footer.addEventListener("mouseout", function () {
        footer.style.backgroundColor = ""; 
    });
});
