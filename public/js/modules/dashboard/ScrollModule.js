const ScrollModule = (function () {

    function initScrollEvents(){
        let lastScrollTop = 0;
        const scrollHideEls = document.querySelectorAll(".hide-on-scroll");

        window.addEventListener("scroll", () => {
            let currentScroll = document.documentElement.scrollTop;

            scrollHideEls.forEach((el) => {
                if (currentScroll > lastScrollTop) {
                    // Scrolling down
                    el.classList.add("scroll-hidden");
                } else {
                    // Scrolling up
                    el.classList.remove("scroll-hidden");
                }
            });

            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
        });
    }
    
    return {
        initScrollEvents: initScrollEvents
    };
}());

export default ScrollModule;
