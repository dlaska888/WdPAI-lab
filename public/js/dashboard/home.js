import GroupModule from "/public/js/modules/GroupModule.js";

export function fetchAllGroups() {
    fetch('http://localhost:8080/link-groups')
        .then(response => response.json())
        .then(async data => {
            const groupsContainer = document.querySelector('.groups-container');
            
            for (const group of data) {
                groupsContainer.appendChild(await GroupModule.render(group))
            }

            // TODO refactor
            const btnLinks = document.querySelectorAll(".btn-group-collapse");
            btnLinks.forEach((btn) => {
                btn.addEventListener("click", () => {
                    const links = btn.closest(".group").querySelector(".group-links");
                    links.classList.toggle("collapse");
                    btn.classList.toggle("active");
                });
            });
        })
        .catch(error => console.error('Error fetching data:', error));
}
