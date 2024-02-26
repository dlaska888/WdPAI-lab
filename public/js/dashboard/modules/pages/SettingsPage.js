import IconModule from "../../modules/IconModule.js";
import ApiClient from "../../ApiClient.js";
import NotificationService from "../../NotificationService.js";
import ModalModule from "../ModalModule.js";
import ChangeUsernameForm from "../forms/ChangeUsernameForm.js";
import ChangePasswordForm from "../forms/ChangePasswordForm.js";
import ChangeProfilePictureForm from "../forms/ChangeProfilePictureForm.js";

const SettingsPage = (function () {
    async function render(pageId) {
        const userData = await fetchUserData();
        const {userName, email} = userData;

        let page = document.createElement("div");
        page.innerHTML = `
            <section id="${pageId}" class="page flex flex-center">
                <div class="profile-container flex-column flex-center">
                    <div class="profile-photo flex flex-center">
                        <img src="/account/profile-picture" alt="Profile picture">
                    </div>
                    <div class="profile-info text-primary text-center">
                        <h1 class="profile-username">${userName || "Username"}</h1>
                        <p class="profile-email">${email || "Email"}</p>
                    </div>
                </div>
                <div class="settings-container flex-column flex-center">
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

        page.querySelector("#btn-change-username")
            .addEventListener("click", () => changeUsernameForm());

        page.querySelector("#btn-change-password")
            .addEventListener("click", () => changePasswordForm());

        const pictureContainer = page.querySelector(".profile-photo")
        const profilePicture = pictureContainer.querySelector("img");
        profilePicture.src = "/account/profile-picture#" + new Date().getTime();
        
        profilePicture.onerror = async () => {
            pictureContainer.innerHTML = await IconModule.render("account");
        };

        pictureContainer.addEventListener("click", async () => await changeProfilePictureForm());

        return page;
    }

    async function updateState() {
        const page = document.querySelector("#page-settings");
        page.replaceWith(await render("page-settings"));
    }

    async function changeUsernameForm() {
        document.body.appendChild(await ModalModule.render(await ChangeUsernameForm.render()));
    }

    async function changePasswordForm() {
        document.body.appendChild(await ModalModule.render(await ChangePasswordForm.render()));
    }

    async function changeProfilePictureForm() {
        document.body.appendChild(await ModalModule.render(await ChangeProfilePictureForm.render()));
    }

    function fetchUserData() {
        return ApiClient.fetchData(`/account`)
            .then(result => {
                if (result.success) return result.data;
                NotificationService.notify(result.message || "Could not get user data", "error")
            })
    }

    return {
        render: render,
        updateState: updateState
    };
}());

export default SettingsPage;