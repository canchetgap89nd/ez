var numeral = require('numeral');
numeral.register('locale', 'vi', {
    delimiters: {
        thousands: '.',
        decimal: ','
    },
    abbreviations: {
        thousand: ' nghìn',
        million: ' triệu',
        billion: ' tỷ',
        trillion: ' nghìn tỷ'
    },
    ordinal: function () {
        return '.';
    },
    currency: {
        symbol: '₫'
    }
});
var langUser = navigator.language || navigator.userLanguage;
if (langUser == 'vi' || langUser == 'vi-VN') {
	numeral.locale('vi');
}

export function formatPrice(price) {
	let pr = parseFloat(price).toLocaleString();
	return numeral(pr).format('0,0');
}

export function valuePrice(price) {
	return numeral(price).value();
}

export {numeral};