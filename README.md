<div align="center">
  <a href="https://github.com/MediaEase/MediaEase">
    <img src="https://github.com/MediaEase/docs/blob/main/assets/mediaease.png" alt="Logo" width="100" height="100">
  </a>  
  <h1>HarmonyUI</h1>
  <p>
    <a href="#about"><strong>Explore the screenshots »</strong></a> ·
    <a href="https://mediaease.github.io/docs/"><strong>Documentation</strong></a> ·
    <a href="https://github.com/MediaEase/HarmonyUI/issues/new?assignees=&labels=bug&template=01_BUG_REPORT.md&title=bug%3A+">Report a Bug</a> ·
    <a href="https://github.com/MediaEase/HarmonyUI/issues/new?assignees=&labels=enhancement&template=02_FEATURE_REQUEST.md&title=feat%3A+">Request a Feature</a> ·
    <a href="https://github.com/MediaEase/HarmonyUI/discussions">Ask a Question</a>
  </p>
</div>

| | |
|---|---|
| **Open&#160;Source** | [![MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://github.com/MediaEase/MediaEase/blob/main/LICENSE) |
| **Community** | [![Pull Requests welcome](https://img.shields.io/badge/PRs-welcome-ff69b4.svg?style=flat-square)](https://github.com/MediaEase/MediaEase/issues?q=is%3Aissue+is%3Aopen+label%3A%22help+wanted%22)  |
| **CI/CD** | (coming soon)  |
| **Maintener** | [![code with love by MediaEase](https://img.shields.io/badge/%3C%2F%3E%20with%20%E2%99%A5%20by-MediaEase-ff1414.svg?style=flat-square)](https://github.com/MediaEase) |
| **Tools** | [![better commits is enabled](https://img.shields.io/badge/better--commits-enabled?style=for-the-badge&logo=git&color=a6e3a1&logoColor=D9E0EE&labelColor=302D41)](https://github.com/Everduin94/better-commits) |

## About HarmonyUI

HarmonyUI is a sophisticated frontend interface for the MediaEase ecosystem, designed to offer a seamless and intuitive user experience for managing and interacting with various server-side services and applications. It emphasizes user-centric design, ensuring that every interaction is both straightforward and efficient.

### Relationship with MediaEase

HarmonyUI is intricately integrated with the MediaEase repository, acting as the frontend interface for the diverse applications and services managed by MediaEase. While MediaEase takes charge of the backend operations and server-side logic, HarmonyUI provides an engaging and interactive graphical interface for users.

## Features

1. **Intuitive User Interface:** HarmonyUI features a user-friendly interface that simplifies the management of MediaEase services, making complex server operations accessible to all users.

2. **Seamless Integration with MediaEase Backend:** It offers flawless integration with the MediaEase backend, ensuring consistent and reliable performance.

3. **Customizable Themes and Layouts:** Users can personalize their experience with various themes and layouts, adapting the interface to their preferences.

4. **Responsive Design:** HarmonyUI is designed to be fully responsive, providing an optimal viewing experience across a wide range of devices.

## Built With

- Symfony 7
- PHP 8.3
- NPM

## Getting Started

### Prerequisites

- A Debian-based machine (Debian 12 recommended) with internet access.
- PHP 8.3 or higher installed on your server.
- Symfony 7 installed globally or locally in your environment.
- Composer for managing PHP dependencies.
- Node.js and NPM for managing JavaScript dependencies.
- Git for cloning the repository.

## Installation

Follow these steps to set up HarmonyUI on your system:

1. **Clone the HarmonyUI Repository:**
   ```bash
   git clone https://github.com/MediaEase/HarmonyUI.git
   ```
2. **Navigate to the HarmonyUI Directory:**
   ```bash
   cd HarmonyUI
   ```
3. **Install Project Dependencies:**
   - Install PHP dependencies using Composer:
     ```bash
     composer install
     ```
   - Install JavaScript dependencies using NPM:
     ```bash
     npm install
     ```
4. **Run Doctrine Migrations:**
   - This step sets up the database schema:
     ```bash
     php bin/console doctrine:database:create
     php bin/console doctrine:migrations:migrate
     ```

## Usage

1. **Create a User Interactively:**
   - Use this command to create a user:
     ```bash
     php bin/console app:create:user
     ```
   - Follow the interactive prompts to set up a new user.
2. **Run HarmonyUI Locally:**
   - Start the Symfony server:
     ```bash
     symfony server:start
     ```

After completing these steps, HarmonyUI will be running locally on your machine. Access it through your web browser at `http://localhost:8000` or the port specified by Symfony.

- For detailed usage instructions, refer to the [User Guide](./USER_GUIDE.md).

## Contributing

Contributions to HarmonyUI are welcome! Please read our [contribution guidelines]((https://github.com/MediaEase/docs/blob/main/docs/CONTRIBUTING.md)).

## License

This project is licensed under the MIT license - see the [LICENSE](https://github.com/MediaEase/HarmonyUI/blob/main/LICENSE) file for details.
