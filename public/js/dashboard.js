import NavigationModule from "./modules/dashboard/NavigationModule.js";
import ScrollModule from "./modules/dashboard/ScrollModule.js";
import MobileNavigationModule from "./modules/dashboard/MobileNavigationModule.js";

await NavigationModule.initNavigation();
MobileNavigationModule.initMobileNavigation();
ScrollModule.initScrollEvents();