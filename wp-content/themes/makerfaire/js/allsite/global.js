/* Provide a class for Safari, the new IE */
if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Mac') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
	// console.log('Safari on Mac detected, applying class...');
	jQuery('html').addClass('safari-mac'); // provide a class for the safari-mac specific css to filter with
}

jQuery(document).ready(function(){
	jQuery('.show-more-snippet').each(function() {
		if(jQuery(this).height() >= 70 ) {
			jQuery(this).after('<a href="#" class="show-more">More...</a>');
			jQuery('.show-more').click(function(event) {
				event.preventDefault();
				if(jQuery(this).prev().css('height') != '70px'){
					jQuery(this).prev().css('max-height', '70px');
					jQuery(this).prev().animate({height: '70px'}, 200);
					jQuery(this).text('More...');
				}else{
					jQuery(this).prev().css({'height':'100%', 'max-height':'none'});
					var xx = jQuery(this).prev().height();
					jQuery(this).prev().css({height:'70px'});
					jQuery(this).prev().stop().animate({height: xx - 80}, 400);
					jQuery(this).text('Less...');
				}
			});
		}
	});
});

// we want the reset email to go through make.co, so we hijacking the button, mutant style
(() => {
  document.addEventListener("DOMContentLoaded", () => {
    const observer = new MutationObserver(() => { // cowabunga dude
      const btn = document.querySelector('button.auth0-lock-submit[aria-label="Send email"]');
      if (btn && !btn.dataset.hijacked) {
        btn.dataset.hijacked = "true";
        btn.textContent = "Send Reset Email";

        // Create a placeholder for custom messages
        let messageBox = document.querySelector("#tenantB-reset-message");
        if (!messageBox) {
          messageBox = document.createElement("div");
          messageBox.id = "tenantB-reset-message";
          messageBox.style.cssText = `
            margin-bottom: 12px;
            font-size: 14px;
            text-align: center;
          `;
          btn.insertAdjacentElement("beforebegin", messageBox);
        }

        btn.addEventListener("click", async (event) => {
          event.preventDefault();

          const emailInput = document.querySelector('input.auth0-lock-input');
          const email = emailInput ? emailInput.value.trim() : "";

          if (!validateEmail(email)) {
            messageBox.textContent = "Please enter your email address.";
            messageBox.style.color = "#e53e3e";
            return;
          }

          messageBox.textContent = "Sending password reset email...";
          messageBox.style.color = "#555";

          try {
            const res = await fetch("https://makermedia.auth0.com/dbconnections/change_password", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                client_id: "J8vRwQOxIEkUOAlxaThb3YBq0ts6Tj0k",
                email: email,
                connection: "DB-Make-Community"
              })
            });

            const text = await res.text();

            if (res.ok) {
              messageBox.textContent = text;
              messageBox.style.color = "#2f855a"; // green
			  setTimeout(() => {
					const closeBtn = document.querySelector('.auth0-lock-close-button');
					if (closeBtn) closeBtn.click();
				}, 3000);
            } else {
              messageBox.textContent = text;
              messageBox.style.color = "#e53e3e"; // red
            }
          } catch (err) {
            console.error("Error sending makermedia reset:", err);
            messageBox.textContent = "Error sending reset email. Please try again.";
            messageBox.style.color = "#e53e3e";
          }
        });
		// back button needs to change things back
		const backBtn = document.querySelector('.auth0-lock-back-button');
		if (backBtn && !backBtn.dataset.backAttached) {
		backBtn.dataset.backAttached = "true";
		backBtn.addEventListener("click", () => {
			const btn = document.querySelector('button.auth0-lock-submit[aria-label="Send email"]');
			if (btn) {
				btn.dataset.hijacked = "false"; 
				btn.textContent = "LOG IN";
			}
			const messageBox = document.querySelector("#tenantB-reset-message");
			if (messageBox) messageBox.remove();
		});
		}
      }
    });

    observer.observe(document.body, { childList: true, subtree: true });
    setTimeout(() => observer.disconnect(), 30000);
  });
})();

function validateEmail(email) {
  const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
}