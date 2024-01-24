import IconModule from "../IconModule.js";
import ApiClient from "../../ApiClient.js";
import NotificationService from "../../NotificationService.js";
import ModalModule from "../ModalModule.js";
import ChangeUsernameForm from "../forms/ChangeUsernameForm.js";
import ChangePasswordForm from "../forms/ChangePasswordForm.js";

const SettingsPage = (function () {
    async function render(pageId) {
        const userData = await fetchUserData();
        const {userName, email} = userData;
        
        let page = document.createElement("div");
        page.innerHTML = `
            <section id="${pageId}" class="page flex flex-center">
                <div class="profile-container flex-column flex-center hide-mobile">
                    <div class="profile-photo flex flex-center">
                    </div>
                    <div class="profile-info text-primary text-center">
                        <h1>${userName || "Username"}</h1>
                        <p>${email || "Email"}</p>
                    </div>
                </div>
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
        
        page.querySelector("#btn-change-username")
            .addEventListener("click", () => changeUsernameForm());

        page.querySelector("#btn-change-password")
            .addEventListener("click", () => changePasswordForm());

        const pictureSrc = await getUserPictureSource();
        const pictureContainer = page.querySelector(".profile-photo");
        
        if (!pictureSrc) {
            pictureContainer.innerHTML = await IconModule.render("account");
            return page;
        }

        const img = document.createElement("img");
        img.src = pictureSrc;

        pictureContainer.appendChild(img);
        
        return page;
    }
    
    function updateState(){
        document.querySelector(".page-settings").click();
    }
    
    async function changeUsernameForm(){
        document.body.appendChild(await ModalModule.render(await ChangeUsernameForm.render()));
    }

    async function changePasswordForm(){
        document.body.appendChild(await ModalModule.render(await ChangePasswordForm.render()));
    }

    function fetchUserData() {
        return ApiClient.fetchData(`http://localhost:8080/account`)
            .then(result => {
                if (result.success) return result.data;
                NotificationService.notify(result.message || "Could not get user data", "error")
            })
    }

    function getUserPictureSource() {
        return ApiClient.fetchFile(`http://localhost:8080/account/profile-picture`)
            .then(result => {
                console.log(result);
                if (result.success) return `/account/profile-picture`;
            })
    }

    return {
        render: render,
        updateState: updateState
    };
}());

export default SettingsPage;
