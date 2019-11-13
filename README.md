# JSE Telegram Bot

A bot that uses the JSE captcha to prevent spam on Telegram channels. The bot can be invited into your channel with the username @jsetelegrambot and will require admin priviledges.

When a new user joins they will receive the message:
"Hello @newUsername, please complete the captcha at https://jsecoin.com/telegram-captcha.php?u=123 within the next 3 minutes and before posting a message. This is to prevent spam on the Telegram channel."

# About

The bot runs on node.js and uses the telegraf framework to connect to the Telegram API.

The PHP code is also included which requires curl to do a GET request to check the user has completed the captcha.

Feel free to fork, edit and alter or improve upon.

## Installation

```bash
npm install
npm start

```
---


## Bug Bounty
This is an initial push alot of cleanup is still required if you spot an issue please report it and if we consider it a major issue we will credit your account as part of our bug bounty offering.
[Bug Bounty Info Page](https://jsecoin.com/en/oddJobs/bugBounty)

## Contribute
If you'd like to assist and help the project please contact us via telegram https://web.telegram.org/#/im?p=@jsetelegram

## License
This project is under the [GNU General Public License v3.0](./LICENSE.md).