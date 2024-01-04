import NavigationModule from "./modules/dashboard/NavigationModule.js";
import ScrollModule from "./modules/dashboard/ScrollModule.js";
import MobileNavigationModule from "./modules/dashboard/MobileNavigationModule.js";

MobileNavigationModule.initMobileNavigation();
ScrollModule.initScrollEvents();
await NavigationModule.initNavigation();
