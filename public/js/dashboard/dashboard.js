import NavigationHandler from "./NavigationHandler.js";
import ScrollHandler from "./ScrollHandler.js";
import MobileNavigationHandler from "./MobileNavigationHandler.js";
import SessionRefresher from "./SessionRefresher.js";

await MobileNavigationHandler.initMobileNavigation();
ScrollHandler.initScrollEvents();
await NavigationHandler.initNavigation();
new SessionRefresher();
