import {getCookie, deleteCookie} from '../helpers/cookies.js'
export default {
	state: {
		api_token: null,
		user_id: null
	},
	initialize() {
		this.state.api_token = getCookie('tk_cs');
		this.state.user_id = getCookie('uid');
	},
	remove() {
		deleteCookie('tk_cs');
		deleteCookie('uid');
		this.initialize();
	}
}