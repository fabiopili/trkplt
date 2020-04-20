TRKPLT

## About

TRKPLT is a tool that allows developers to test a website for resources that would be blocked by the most common ad blocking lists. [https://trkplt.com](https://trkplt.com)

## Let's talk!
[https://fabiopili.com](https://fabiopili.com)

## Technologies used

- Vue.js as the frontend framework for the web app.
- NPM as the module bundler, webpack, SASS, svg-spritemap-webpack-plugin for handling frontend assets.
- Node.js, Express and Javascript to run the intermediary API that acts as a bridge between the main app logic and the headless browsers. This API is bound to localhost and not accessible publicly.
– Puppeteer for interacting with the headless Chrome browser workers.
- Modern PHP and the Laravel framework for the public-facing API and all main app logic like: routing, controllers, models, ORM for database interaction, event bus, communication with queues, etc.
- Redis for queues and caching.
- Docker for the local development environment.
- Git for version control and Git Flow for coordinating feature and hotfix branches.
- DigitalOcean droplet running Ubuntu 18.04.3, PHP 7.4, Node.js v13.x, nginx, MariaDB, Redis for production hosting.

## Why

I noticed that a lot of web apps break completely for users with ad blocking enabled. And that’s something that I had to face myself a few months ago, when I was integrating Google Analytics events with [Move1’s](https://move1.io/) own analytics and data collection pipeline.

Tracking and measuring user behavior is key for taking data-driven decisions, finding opportunities and uncovering bugs. Something that all companies should do. But, sometimes, this is done at the expense of UX.

On the development side, it’s something actually pretty easy to work around. The best practice is to always check if an external library is actually loaded before firing an event and always make sure that errors are caught and don’t impact the main code flow. In other words, to degrade the tracking experience gracefully and avoid breaking the app.

But most developers are not aware of this problem. It’s hard to diagnose and also measure the impact since ad blockers sometimes block the external services we use to identify errors and track user behaviour.

Consider this: depending on the source, between 11% and 47% of all internet users are currently blocking ads. And how much effort do we invest into getting a 1% better conversion rate?

## ToDo

Please consider this project as work-in-progress.

- Code review, better documentation.
- Better Unit testing coverage.

## ToDoLater

- Decouple everything from the base framework, Laravel, and release this repository as an independent package.
- Spin up a few inexpensive cloud instances using Docker to run a small fleet of headless browser workers.

## Roadmap

- Move from periodic polling to proper WebSocket communication with the backend API.
- Record additional information for each request: time, size, etc.
- Play with data visualization.
- Parse domains from lists in filter format (DOM), like EasyList.
- Allow users to customize the ad blocking lists used for testing.
- Script more advanced browser interactions, such as submitting a login page.

## License

This is a personal showcase project and, for now, all rights are reserved. I’ll probably open it under a Creative Commons License in the future, after decoupling all source code from the base framework.

For all packages and frameworks used, please refer to their first-party repositories for licensing information.
