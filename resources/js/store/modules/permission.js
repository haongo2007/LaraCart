import { asyncRoutes, constantRoutes,menuSidebar } from '@/router';

/**
 * Check if it matches the current user right by meta.role
 * @param {String[]} roles
 * @param {String[]} permissions
 * @param route
 */
function canAccess(roles, permissions, route) {
  if (route.meta) {
    let hasRole = true;
    let hasPermission = true;
    if (route.meta.roles || route.meta.permissions) {
      // If it has meta.roles or meta.permissions, accessible = hasRole || permission
      hasRole = false;
      hasPermission = false;
      if (route.meta.roles) {
        hasRole = roles.some(role => route.meta.roles.includes(role));
      }

      if (route.meta.permissions) {
        hasPermission = permissions.some(permission => route.meta.permissions.includes(permission));
      }
    }
    return hasRole || hasPermission;
  }

  // If no meta.roles/meta.permissions inputted - the route should be accessible
  return true;
}

/**
 * Find all routes of this role
 * @param routes asyncRoutes
 * @param roles
 */
function filterAsyncRoutes(routes, roles, permissions) {
  const res = [];

  routes.forEach(route => {
    const tmp = { ...route };
    if (canAccess(roles, permissions, tmp)) {
      if (tmp.children) {
        tmp.children = filterAsyncRoutes(
          tmp.children,
          roles,
          permissions
        );
      }
      res.push(tmp);
    }
  });

  return res;
}

const state = {
  routes: [],
  addRoutes: [],
  menus:[],
};

const mutations = {
  SET_ROUTES: (state, routes) => {
    state.addRoutes = routes;
    state.routes = constantRoutes.concat(routes);
  },
  SET_MENUS: (state, menus) => {
    state.menus = menus;
  },
};

const actions = {
  generateRoutes({ commit }, { roles, permissions }) {
    return new Promise(resolve => {
      let accessedRoutes;
      let accessedMenus = menuSidebar;
      if (roles.includes('Administrator')) {
        accessedRoutes = asyncRoutes || [];
      } else {
        accessedRoutes = filterAsyncRoutes(asyncRoutes, roles, permissions);
        accessedMenus = filterAsyncRoutes(menuSidebar, roles, permissions);
      }

      commit('SET_MENUS', accessedMenus);
      commit('SET_ROUTES', accessedRoutes);
      resolve(accessedRoutes);
    });
  },
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
};
