import NavigationModule from "./modules/NavigationModule.js";
import ScrollModule from "./modules/ScrollModule.js";
import MobileNavigationModule from "./modules/MobileNavigationModule.js";

MobileNavigationModule.initMobileNavigation();
ScrollModule.initScrollEvents();
await NavigationModule.initNavigation();
