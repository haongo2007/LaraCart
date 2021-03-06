import { login, logout, getInfo } from '@/api/auth';
import { isLogged, setLogged, removeToken } from '@/utils/auth';
import router, { resetRouter } from '@/router';
import Cookies from 'js-cookie';

const defaultState = () => {
  return {
    token_name: 'server:full',
    id: null,
    user: null,
    token: isLogged(),
    name: '',
    avatar: '',
    introduction: '',
    roles: [],
    permissions: [],
    online: false,
    storeList:[],
    currentStore:null,
  }
}

const state = defaultState();

const mutations = {
  SET_ID: (state, id) => {
    state.id = id;
  },
  SET_TOKEN: (state, token) => {
    state.token = token;
  },
  SET_INTRODUCTION: (state, introduction) => {
    state.introduction = introduction;
  },
  SET_NAME: (state, name) => {
    state.name = name;
  },
  SET_AVATAR: (state, avatar) => {
    state.avatar = avatar;
  },
  SET_ROLES: (state, roles) => {
    state.roles = roles;
  },
  SET_PERMISSIONS: (state, permissions) => {
    state.permissions = permissions;
  },
  SET_STORE_LIST: (state, store) => {
    state.storeList = store;
  },
  SET_STORE_CURRENT: (state, store) => {
    if (typeof store == 'string') {
      store = JSON.parse(store);
    }
    state.currentStore = store.length == 1 ? store[0] : null;
    Cookies.set('store', store);
  },
  RESET_STORE (state) {
    Object.assign(state, defaultState())
  }
};

const actions = {
  // acction change store
  ChangeStore({ commit, state }, store){
    commit('SET_STORE_CURRENT', store);
  },

  // user login
  login({ commit, state }, userInfo) {
    const { email, password } = userInfo;
    return new Promise((resolve, reject) => {
      login({ email: email.trim(), password: password, token_name: state.token_name })
        .then(response => {
          setLogged(response.data.token);
          resolve(response);
        })
        .catch(error => {
          console.log(error);
          reject(error);
        });
    });
  },

  // get user info
  getInfo({ commit, state }) {
    return new Promise((resolve, reject) => {
      getInfo()
        .then(response => {
          const { data } = response;

          if (!data) {
            reject('Verification failed, please Login again.');
          }

          const { roles, name, avatar, introduction, permissions, id,store } = data;
          // roles must be a non-empty array
          if (!roles || roles.length <= 0) {
            reject('getInfo: roles must be a non-null array!');
          }
          commit('SET_ROLES', roles);
          commit('SET_PERMISSIONS', permissions);
          commit('SET_NAME', name);
          commit('SET_AVATAR', avatar);
          commit('SET_INTRODUCTION', introduction);
          commit('SET_ID', id);
          commit('SET_STORE_LIST', store);
          if (!roles.includes('administrator')) {
            // let first = store.splice(0,1);
            commit('SET_STORE_CURRENT', Object.keys(store)[0]);
          }
          resolve(data);
        }).catch(error => {
          reject(error);
        });
    });
  },

  // user logout
  logout({ commit }) {
    return new Promise((resolve, reject) => {
      logout()
        .then(() => {
          commit('RESET_STORE', []);
          removeToken();
          resetRouter();
          resolve();
        })
        .catch(error => {
          reject(error);
        });
    });
  },

  // remove token
  resetToken({ commit }) {
    return new Promise(resolve => {
      commit('SET_TOKEN', '');
      commit('SET_ROLES', []);
      removeToken();
      resolve();
    });
  },

  // Dynamically modify permissions
  changeRoles({ commit, dispatch }, role) {
    return new Promise(resolve => {
      // const token = role + '-token';

      // commit('SET_TOKEN', token);
      // setLogged(token);

      // const { roles } = await dispatch('getInfo');

      const roles = [role.name];
      const permissions = role.permissions.map(permission => permission.name);
      commit('SET_ROLES', roles);
      commit('SET_PERMISSIONS', permissions);
      resetRouter();

      // generate accessible routes map based on roles
      const accessRoutes = dispatch('permission/generateRoutes', { roles, permissions }, { root: true });
      // dynamically add accessible routes

      accessRoutes.then((accessRoutes) => {
        router.addRoutes(accessRoutes);
        resolve();
      });
    });
  },
  // update avatar
  setAvatar({ commit }, avatar) {
    commit('SET_AVATAR', avatar);
  },
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
};
