var io = require('socket.io')(6001)
var jwt = require('jsonwebtoken')
var Redis = require('ioredis')
const env = require('./prod.env.js')
var cert = env.SOCKET_SECRET
console.log('Connnected to port 6001')

//middleware
io.use((socket, next) => {
  	let token = socket.handshake.query.token
  	if (isValid(token)) {
    	return next()
  	}
  	return next(new Error('authentication error'))
})

function isValid(token) {
	let check = false
	if (token) {
		jwt.verify(token, cert, (err, decoded) => {
			if (decoded && decoded.iss) {
				check = true
			}
		})
	}
	return check
}

function userOnline(userId, socketId) {
	let redis = new Redis()
	// redis.set('user:online:' + userId, null)
	let client = null
	redis.get('user:online:' + userId, function (err, result) {
		if (err) {
			console.log('error check user online: ' + err)
			return
		}
		if (result) {
			client = JSON.parse(result)
			if (Array.isArray(client)) {
				let index = client.indexOf(socketId)
				if (index == -1) {
					client.push(socketId)
				}
			} else {
				client = [socketId]
			}
		} else {
			client = [socketId]
		}
		let pack = JSON.stringify(client)
		redis.set('user:online:' + userId, pack)
	})
}

function userOffline(userId, socketId) {
	let redis = new Redis()
	let client = null
	redis.get('user:online:' + userId, function(err, result) {
		if (err) {
			console.log('error check user offline: ' + err)
			return
		}
		let pack = [];
		if (result) {
			client = JSON.parse(result)
			if (Array.isArray(client)) {
				let index = client.indexOf(socketId)
				if (index != -1) {
					client.splice(index, 1)
				}
			} else {
				client = []
			}
			pack = JSON.stringify(client)
		}
		redis.set('user:online:' + userId, pack)
	})
}

io.on('error', function(socket) {
	console.log('error')
})
io.on('connection', function(socket) {
	console.log(socket.id + ' connected')
	let token = socket.handshake.query.token
	let userId = null
	if (token) {
		jwt.verify(token, cert, (err, decoded) => {
			if (decoded && decoded.iss) {
				userId = decoded.uid
			}
		})
	}
	if (userId) {
		userOnline(userId, socket.id)
	}

	socket.on('disconnect', function() {
		if (userId) {
			userOffline(userId, socket.id)
		}
	})
})
var redis = new Redis()
redis.psubscribe("*",function(error,count){
	//
})
redis.on('pmessage', function(partner, channel, message) {
	message = JSON.parse(message)
	io.emit(channel + ":" + message.event, message.data)
})