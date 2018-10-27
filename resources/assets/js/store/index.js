import Vue from 'vue'
import Vuex from 'vuex'
import conversation from './modules/conversation'

Vue.use(Vuex)

export default new Vuex.Store({
 	state: {
    CountConverUnread: 0,
    CountOrderNew: 0,
    pages: [],
 		setting: {}, 
 		user: {},
 		role: {},
 		mems: {},
    pack: {},
 		groups: [],
 		quickAns: [],
 		props: [],
 		cates: [],
 		unreadCount: 0,
 		title: '',
    loading: false
	},
	mutations: {
    SET_LOAD (state, load) {
      state.loading = load
    },
    SET_PACK (state, pack) {
      state.pack = pack
    },
    upPages (state, payload) {
    		state.pages = payload;
    },
    upSetting (state, payload) {
    		state.setting = payload;
    },
    upUser (state, payload) {
    		state.user = payload;
    },
    upRole (state, payload) {
    	state.role = payload;
    },
    upMems (state, payload) {
    	state.mems = payload;
    },
    upGroups (state, payload) {
    	state.groups = payload;
    },
    upQuickAns(state, payload) {
    	state.quickAns = payload;
    },
    upProps(state, payload) {
    	state.props = payload;
    },
    upCates(state, payload) {
    	state.cates = payload;
    },
    pushCate(state, payload) {
    	state.cates.push(payload);
    },
    pushProp(state, payload) {
    	state.props.push(payload);
    },
    upUnreadCount(state, payload) {
    	state.unreadCount = payload;
    },
    incrementUnread (state, num = 1) {
    	state.unreadCount += num
    },
    minusUnread (state, num = 1) {
    	state.unreadCount -= num
    },
    clearUnread (state) {
    	state.unreadCount = 0
    }
	},
	actions: {
    setLoad ({commit}, load = true) {
      commit('SET_LOAD', load)
    },
		incrementUnread ({commit}, num) {
			commit('incrementUnread', num)
		},
		minusUnread ({commit}, num) {
			commit('minusUnread', num)
		},
		clearUnread ({commit}) {
			commit('clearUnread')
		},
		doNotify ({commit}, message) {
			if (message.type) {
				$.notify(message.text, message.type)
			} else {
				$.notify(message, 'success')
			}
		},
    upPages ({commit}, pages) {
      commit('upPages', pages)
    },
    upSetting ({commit}, setting) {
      commit('upSetting', setting)
    },
    upUser ({commit}, user) {
      commit('upUser', user)
    },
    upRole ({commit}, role) {
      commit('upRole', role)
    },
    upMems ({commit}, mems) {
      commit('upMems', mems)
    },
    upGroups ({commit}, groups) {
      commit('upGroups', groups)
    },
    upQuickAns ({commit}, quickAns) {
      commit('upQuickAns', quickAns)
    },
    upProps ({commit}, props) {
      commit('upProps', props)
    },
    upCates ({commit}, cates) {
      commit('upCates', cates)
    },
    pushCate ({commit}, cate) {
      commit('pushCate', cate)
    },
    pushProp ({commit}, prop) {
      commit('pushProp', prop)
    },
    setPack ({commit}, pack) {
      commit('SET_PACK', pack)
    }
	},
	getters: {
    loading: state => {
      return state.loading
    },
		role: state => {
			return state.role
		},
		user: state => {
			return state.user
		},
		pages: state => {
			return state.pages
		},
    mems: state => {
      return state.mems
    },
    setting: state => {
      return state.setting
    },
    package: state => {
      return state.pack
    }
	},
	modules: {
		conversation
	}
})