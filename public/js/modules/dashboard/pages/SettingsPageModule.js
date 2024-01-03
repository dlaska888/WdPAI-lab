// TODO add functionality to buttons

import IconModule from "../IconModule.js";

const LinkPageModule = (function () {
    async function render() {
        let page = document.createElement("div");
        page.innerHTML = `
            <section id="page-settings" class="flex flex-center">
                <div class="profile-container flex-column flex-center hide-mobile">
                    <div class="profile-photo">
                        ${await IconModule.render("account")}
                    </div>
                    <div class="profile-info text-primary text-center">
                        <h1>Silvio Suresh</h1>
                        <p>sureshsilvio@gmail.com</p>
                    </div>
                </div>
                <div class="line-vertical-primary hide-mobile"></div>
                <div class="settings-container flex-column">
                    <button id="btn-change-username" class="btn-primary" title="Change Username">
                        <span class="btn-primary-top">Change Username</span>
                    </button>
                    <button id="btn-change-password" class="btn-primary" title="Change Password">
                        <span class="btn-primary-top">Change Password</span>
                    </button>
                    <button id="btn-resend-verification" class="btn-primary" title="Resend Verification">
                        <span class="btn-primary-top">Resend Verification Email</span>
                    </button>
                    <button id="btn-enable-2fa" class="btn-primary" title="Enable 2FA">
                        <span class="btn-primary-top">Enable 2FA</span>
                    </button>
                    <div id="btn-delete-account" class="btn-primary" title="Delete Account">
                        <span class="btn-primary-top">Delete Account</span>
                    </div>
                </div>
            </section>`;
        page = page.firstElementChild;
        return page;
    }

    return {
        render: render,
    };
}());

export default LinkPageModule;
