import moment from 'moment'

export function momentLocale(time) {
	return moment(time).locale('vi');
}