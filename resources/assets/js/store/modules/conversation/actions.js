import { get } from '../../../helpers/send'
export default {
	setCustomerOnChat ({commit}, customer) {
		commit('SET_CUSTOMER_ON_CHAT', customer)
	},
	getPhone ({commit, dispatch, state}, psid) {
		this.dispatch('setLoad', true, {root: true})
		get('api/convert-uid/psid-to-phone/' + psid, {
			app: 'WEB'
		})
			.then((res) => {
				this.dispatch('setLoad', false, {root: true})
				if (res.data.phone) {
					dispatch('setPhoneBoard', true)
					dispatch('setPhoneNumber', res.data.phone)
				}
			})
			.catch((err) => {
				this.dispatch('setLoad', false, {root: true})
				if (err.response.status == 400) {
					let message = err.response.data.message
					$.notify(message, 'error')
				}
			})
	},
	getLink ({commit, dispatch}, psid) {
		this.dispatch('setLoad', true, {root: true})
		get('api/convert-uid/psid-to-link/' + psid, {
			app: 'WEB'
		})
			.then((res) => {
				this.dispatch('setLoad', false, {root: true})
				if (res.data.link) {
					let url = res.data.link
					window.open(url,'_blank')
				}
			})
			.catch((err) => {
				this.dispatch('setLoad', false, {root: true})
				if (err.response.status == 400) {
					let message = err.response.data.message
					$.notify(message, 'error')
				}
			})
	},
	setPhoneBoard ({commit}, phoneBoard = true) {
		commit('SET_PHONE_BOARD', phoneBoard)
	},
	setPhoneNumber ({commit}, phoneNumber) {
		commit('SET_PHONE_NUMBER', phoneNumber)
	},
}