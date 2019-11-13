const Telegraf = require('telegraf')
const request = require('request');

const version = `v0.1.0`;

const credentials = require('./credentials.json'); // {"token":"123"} https://core.telegram.org/bots

const bot = new Telegraf(credentials.token)

let newUsers = [];

bot.on('message', async (ctx) => {
	const chatID = ctx.update.message.chat.id;
	//const chatTitle = ctx.update.message.chat.title;
	const msgID = ctx.update.message.message_id;
	const msg = ctx.update.message.text;
	//console.log(ctx.update.message);
	//console.log('-------------------');
	// delete bad words
	if (ctx.update.message.text && msg.match(/when lambo/i)) {
		ctx.telegram.deleteMessage(chatID, msgID);
		console.log(`DELETED: ${chatID} - ${msgID}`);
	}
	// message new users about captcha
	if (ctx.update.message.new_chat_members) {
		ctx.update.message.new_chat_members.forEach(async (newUser) => {
			const introMessage = `Hello @${newUser.username}, please complete the captcha at https://jsecoin.com/telegram-captcha.php?u=${newUser.id} within the next 3 minutes and before posting a message. This is to prevent spam on the Telegram channel.`;
			const introMsgTicket = await ctx.reply(introMessage);
			//console.log(introMsgTicket);
			const introMsgID = introMsgTicket.message_id;
			setTimeout(() => {
				ctx.telegram.deleteMessage(chatID, introMsgID);
			}, 180000);

			newUsers.push(newUser.id);
			setTimeout(() => {
				checkCaptcha(newUser.id);
			}, 10000);
			setTimeout(() => {
				kickUser(ctx,chatID,newUser.id);
			}, 180000);
		});
	}
	// block new users
	if (ctx.update.message.text && newUsers.indexOf(ctx.update.message.from.id) > -1) {
		ctx.telegram.deleteMessage(chatID, msgID);
		console.log(`DELETED: ${chatID} - ${msgID}`);
	}
});

/* check if the user has completed the captcha and remove from array if so */
const checkCaptcha = ((userID) => {
	if (newUsers.indexOf(userID) === -1) return false;
	console.log(`Checking Captcha: ${userID}`);
	request(`https://jsecoin.com/telegram-captcha.php?c=${userID}`, function (error, response, body) {
		if (body) {
			if (body === '1') {
				newUsers = newUsers.filter(e => e !== userID);
				console.log(`Captcha Passed: ${userID}`);
			}
		}
		if (error) {
			console.log('Error 61 checkCaptcha server error: '+error);
			newUsers = []; // clear by default on error, potential threat of DoS but better than blocking out users.
		}
		setTimeout(() => {
			checkCaptcha(userID);
		}, 10000);
	});
});

/* kick user if they haven't done captcha in 3 mins */
const kickUser = ((ctx,chatID,userID) => {
	if (newUsers.indexOf(userID) > -1) {
		console.log(`Kicking User: ${userID} - ${chatID}`);
		newUsers = newUsers.filter(e => e !== userID);
		ctx.kickChatMember(userID,chatID);
	}
});

bot.startPolling()

console.log(`JSE Telegram Bot ${version} Started`);
