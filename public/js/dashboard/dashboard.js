import NavigationHandler from "./NavigationHandler.js";
import ScrollHandler from "./ScrollHandler.js";
import MobileNavigationHandler from "./MobileNavigationHandler.js";

MobileNavigationHandler.initMobileNavigation();
ScrollHandler.initScrollEvents();
await NavigationHandler.initNavigation();
