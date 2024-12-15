# Changelog for version 0.51.14-alpha

## 🎉 New Features

- `9590c3a` (wip) Custom application wizard (@tomcdj71)
- `5643b9f` add ManageCronCommand (@tomcdj71)
- `813532c` autogen the app listing based on the apps really available when installing MediaEase (@tomcdj71)
- `d6f8f54` add the CreateUserType form in the modale to create an user (@tomcdj71)
- `53c65e2` add a context to the RegistrationHandler (@tomcdj71)
- `06c6975` Admin can now approve new registrations (@tomcdj71)
- `9eb48c7` Admin can now ban/unban users (@tomcdj71)
- `9ec4917` add commands to interact with IPs and store it securely (@tomcdj71)
- `c508184` Access logs page (@tomcdj71)
- `88956a1` Access logs methods and primary design (@tomcdj71)
- `bcbdadc` Application Log viewer (@tomcdj71)
- `77643f8` add Symfony Commands to quickly inspect log files (@tomcdj71)
- `da8b75f` (General Settings) add fields for changing Website Identity (@tomcdj71)
- `99a31cb` add commands to quickly control feature flags (@tomcdj71)
- `9d34954` create Twig\FeatureFlagExtension (@tomcdj71)
- `5365ea1` (wip) Logs pages (@tomcdj71)
- `9812642` Users List page (wip) (@tomcdj71)
- `5fb983a` (App Sotre) use user's verbosity preference when running zen scripts (@tomcdj71)
- `9cec925` (api) error messages are now more expended (@tomcdj71)
- `d5f8ca8` Admins can now enable or disable the transcoder for each media centrs installed (@tomcdj71)
- `89d926b` fine tune registration process (@tomcdj71)
- `571565a` final user profile page ! (@tomcdj71)
- `8ce5665` IconButtonExtension (@tomcdj71)
- `0c49d00` IconButtonExtension (@tomcdj71)
- `d2bd7eb` ImageResetController (@tomcdj71)
- `92724a5` user Avatar and user Backdrop customization (@tomcdj71)
- `a707dcb` add the App/Service/Image classes (@tomcdj71)
- `628734c` add ResetDefaultImageService (@tomcdj71)
- `2d3eecb` create the ImageUploaderService (@tomcdj71)
- `68aa542` symfony/ux-dropzone (@tomcdj71)
- `3d9a2ad` add new settings (@tomcdj71)
- `37e05ac` app groups settings (@tomcdj71)
- `856a04f` php configurator (@tomcdj71)
- `b1041b1` smtp configurator (@tomcdj71)
- `416c9f3` smtp configurator (@tomcdj71)
- `378d41a` DotEnvUpdater - add interractions with dotenv file (@tomcdj71)
- `08b690f` Security/SecretManager - add interactions with the symfony vault (@tomcdj71)
- `2d51c56` admins can edit their SMTP settings directly from the ui (@tomcdj71)
- `b13578d` settings pages (wip) (@tomcdj71)
- `5d430d8` add create:base-groups command (@tomcdj71)
- `099a920` add  command for creating the first user during the setup (@tomcdj71)
- `e755f0d` add just sf-rotate-keys to rotate secret keys in the .env.local file (@tomcdj71)
- `62f093d` add make sf-rotate-keys to rotate secret keys in the .env.local file (@tomcdj71)
- `2d318d8` add secret:regenerate-mercure-jwt-secret command (@tomcdj71)
- `01cc0ea` add secret:regenerate-jwt-passphrase command (@tomcdj71)
- `508c4e3` add secret:regenerate-app-secret command (@tomcdj71)
- `9815e68` delete a service self owned (@tomcdj71)
- `37e6788` add /store/install route (@tomcdj71)
- `c0093db` add store api routes (@tomcdj71)
- `e33ea99` add quick actions button in the appstore (@tomcdj71)
- `4cd7d68` add AppStore (@tomcdj71)

## 🩹 Bug Fixes

- `af32ed2` regenerate commands was missing their argument (@tomcdj71)
- `28324cd` regenerate token commands (@tomcdj71)
- `38e9ccd` PNPM usage in Makefile (@tomcdj71)
- `0228735` remove Multiple Schema Definition (@tomcdj71)
- `1fe30a4` correcctly bypass invalid app config.yaml if requested (@tomcdj71)
- `2ae454a` clear proper environment when user make/just install-project command (@tomcdj71)
- `5177e82` check if skip_requirements_checks argument is found in the app config.yaml (@tomcdj71)
- `c5df197` use getPreferences() instead wrong getPrefrefence() in the profile macros (@tomcdj71)
- `7cc0ee3` change the command used in CommandExecutorService for managing the sudoers file (@tomcdj71)
- `048c9e9` use the proper Assert in the User Entity (@tomcdj71)
- `c06bf65` lock passwords to 30 characters max (@tomcdj71)
- `a44282a` add missing  in the ResetPasswordController (@tomcdj71)
- `91e8b3d` add forgot-password link in the login screen (@tomcdj71)
- `f388604` add missing fields in DataFixtures (@tomcdj71)
- `981c4ca` Users in User Page list should be sorted alphabetically and Admin should be in first position (@tomcdj71)
- `4f325bc` really hide console if verbosity is not enabled (@tomcdj71)
- `bda1ca6` (App Store)  condition was wrong, causing the a failed detection (@tomcdj71)
- `f24546d` (api) ->getGroup() serialization group (@tomcdj71)
- `fc42afc` findMyProfile() method was not adding the transcode section (@tomcdj71)
- `a390a9b` Transcode entity relation with the Service entity (@tomcdj71)
- `a1cc516` use correct route redirect when deletin a mount path (@tomcdj71)
- `e8be13a` use the 'date' filter for activatedAt report (@tomcdj71)
- `2330a4f` use handle method instead of deprecated handleFormSubmission (@tomcdj71)
- `56d9bcf` add new setting in the fixture (@tomcdj71)
- `1bf85ff` add  variable in the controller methods (@tomcdj71)
- `3a42208` add  variable in the controller methods (@tomcdj71)
- `586ebf3` add missing upload directories (@tomcdj71)
- `bc3933b` use the correct path for the uplaoder service (@tomcdj71)
- `675af16` (rector) skip Store Entity instead Group Entity (@tomcdj71)
- `df9b9ee` Use Entity now use the right property (@tomcdj71)
- `920fabd` send the settings in the login page for better branding (@tomcdj71)
- `65879f7` use correct route for jwt token creation (@tomcdj71)
- `1114f2d` better root url on Service Fixxtures (@tomcdj71)
- `707c5a1` use correct help/label keys on PhpType (@tomcdj71)
- `344f316` catch turbostream exception (@tomcdj71)
- `70ab8d9` add missing login.js asset (@tomcdj71)
- `0249d84` reintroduce RegistrationForm (@tomcdj71)
- `64f8085` clear dev cache when installing project (@tomcdj71)
- `0d627e4` add WidgetFixtures to prod group (@tomcdj71)
- `29e0fa1` change temp file lock path (@tomcdj71)
- `9bc0764` change temp file path (@tomcdj71)
- `a90cfba` load fixtures only when running the MediaEase installer (@tomcdj71)
- `875829c` remove MountFixtures from the prod fixtures groups (@tomcdj71)
- `0357a15` Makefile (@Justfile - remove non-interactive flag on npm install|tomcdj71)
- `8189e33` rector imports (@tomcdj71)
- `6a9b289` missing { causing failures (@tomcdj71)
- `d5d5f42` remove old console.log (@tomcdj71)
- `26313f5` session handling (@tomcdj71)
- `0811cdb` add missing fields in composer.json (@tomcdj71)
- `1ae8e0a` Makefile validation (@tomcdj71)
- `5e21d9f` click should send the request once (@tomcdj71)
- `e8dbd6e` wrong field used to retrieve pinnedApps (@tomcdj71)
- `8e2e594` api route schema (@tomcdj71)

## 📡 API

- `d6095df` add missing fields to comply with redocly (@tomcdj71)
- `1e4c303` add new serialization groups for a better Preferences properties retrieval (@tomcdj71)
- `49178bd` add missing properties in User.item schema definition (@tomcdj71)
- `a197079` add  endpoint for the future logs report (@tomcdj71)
- `294c4a0` add new /settings endpoint (@tomcdj71)
- `5e629ca` use openapi v3.1.0 specs (@tomcdj71)
- `b1a68c9` add sort filter on /api/apps route (@tomcdj71)
- `9b95d5b` global refactor api routes (@tomcdj71)
- `1f8e076` change /api/login_check rounte to /api/auth/login (@tomcdj71)
- `5c524cb` add new routes for managing groups (@tomcdj71)
- `8abd05e` add access_groups routes (@tomcdj71)
- `430d2aa` fix api endpoints (@tomcdj71)

## 🍱 Assets

- `6873537` conditionaly display/hide card's app finder depending of the current view (@tomcdj71)
- `9d411c6` add registration.js (@tomcdj71)
- `4455277` fix images paths accoring to the bug introduced in last commit (@tomcdj71)
- `3f2b52d` refactor AppManager (@tomcdj71)
- `e96d671` use new variables on AppStore module (fix api information retrieval) (@tomcdj71)
- `855cb5e` ensure updateWidget and headers are imported (@tomcdj71)
- `8a006f6` ensure theme_switcher uses the new api endpoint (@tomcdj71)
- `d6ccdd3` change api call (@tomcdj71)
- `737f1fc` refactor pin files (@tomcdj71)
- `b66e1f2` refactor main entrypoints (@tomcdj71)
- `3127750` rename vpn.png to openvpn.png (@tomcdj71)
- `a967269` create public/brand directory (@tomcdj71)
- `25c46ee` update npm lock files (@tomcdj71)
- `b14375d` init TailwindElements's Tooltip library (@tomcdj71)
- `706e3c5` add docblocks to JS files (@tomcdj71)
- `8cc6df1` prepare assets for better SRP (@tomcdj71)

## 🏗️ Build System & Dependencies

- `84c91ea` update composer packages to latest versions (@tomcdj71)
- `d876e81` upgrade dependencies and lock zircote/swagger-php to v4.10.3 until bug is fixed (@tomcdj71)
- `b825c79` add symfonycasts/dynamic-forms and symfony/ux-autocomplete packages (@tomcdj71)
- `12b4ae5` update sass and webpack packages to latest (@tomcdj71)
- `937ee1e` add BazingaJsTranslationBundle to translate strings generated by the JS (@tomcdj71)
- `a95c17d` remove ApplicationFixtures.php as not needed anymore (@tomcdj71)
- `dc756c6` dont depend of ApplicationFixtures anymore (@tomcdj71)
- `a682c9c` upgrade npm dependencies (@tomcdj71)
- `86f202f` upgrade phpstan to v11.2.0 (@tomcdj71)
- `94be02d` update sass dependency to v1.77.4 (@tomcdj71)
- `3315711` update phpmetrics/phmetrics to v3.0.0rc6 (@tomcdj71)
- `b2cc17f` update Symfony dependencies to v7.1.x (@tomcdj71)
- `c792604` add flagception/flagception-bundle for future feature flags feature in MediaEase (@tomcdj71)
- `e1e7087` upgrade nelmio/api-doc-bundle to latest version (@tomcdj71)
- `fa1016e` upgrade symfony/var-exporter to latest version (@tomcdj71)
- `2fc8452` (dev) update rector to latest version (@tomcdj71)
- `3c0db40` update symfony-ux packages to latest (@tomcdj71)
- `72faeb7` update npm dependencies (@tomcdj71)
- `79146d3` build svg icons during initial build step (Makefile or Justfile (@tomcdj71)
- `1c0aa82` remove support of old Ionicon/Heroicon resolver (@tomcdj71)
- `139560b` add symfony/ux-icons (@tomcdj71)
- `67ec940` add symfonycasts/reset-password-bundle (@tomcdj71)
- `c638f9b` update composer dependencies (@tomcdj71)
- `87125af` update node dependencies (@tomcdj71)
- `4920a9d` require GD extension (@tomcdj71)
- `3991132` add liip/imagine-bundle for easy uploading images (@tomcdj71)
- `c9d9da0` (rector) skip Group Entity (@tomcdj71)
- `c5174aa` (rector) skip User Entity (@tomcdj71)
- `fab7dae` add symfony/ux-dropzone package (@tomcdj71)
- `cba504a` downgrade symfony/var-exporter to avoid bug introduced in v7.0.4 (@tomcdj71)
- `444bfaa` upgrade phpunit to v11.1 (@tomcdj71)
- `df97f87` upgrade nelmio-api-docs v4.25 (@tomcdj71)
- `de8a399` add Pyrrah/GravatarBundle as dependency (@tomcdj71)
- `864d687` correctly setup subdomain fixtures (@tomcdj71)
- `ce93a14` add TomSelect.js to the required dependencies (@tomcdj71)
- `b09a22b` enhance the subdomain generation in fixtures (@tomcdj71)
- `41f608b` upgrade composer and js dependencies (@tomcdj71)
- `3862015` update main app.js file (@tomcdj71)
- `1067364` group fixtures (@tomcdj71)
- `fc18299` remove qa-composer-outdated from install-project command (@tomcdj71)
- `3a12d57` downgrade doctrine/orm version for stability (@tomcdj71)
- `b1b72d0` add phpcs into dev packages (@tomcdj71)
- `3dbb16d` upgrade all composer dependencies (@tomcdj71)
- `ecdc7b1` update HarmonyUI dependencies (@tomcdj71)
- `63bee90` update Justfile and Makefile (@tomcdj71)
- `619b4f9` Makefile updated (@tomcdj71)
- `15ecdde` update composer dependencies (@tomcdj71)

## 🚀 Chores

- `8da36f2` regenerate tokens when installing HarmonyUI with make or just (@tomcdj71)
- `4d25ef2` add missing timestamps when registering the first user (@tomcdj71)
- `2e01063` add registration IP when registering the first user (@tomcdj71)
- `0ab7a71` update MediaEase paths [skip ci] (@tomcdj71)
- `1bd372b` add  and  fields in the Store entity (@tomcdj71)
- `4510d60` add new feature flags (@tomcdj71)
- `ac0b9a8` update outro message (@tomcdj71)
- `5175a92` add missing column definition (@tomcdj71)
- `c95e1be` move remaining commands to the General namespace (@tomcdj71)
- `8e8e73d` change Command naming to regroup it in an harmony command listing (@tomcdj71)
- `81c2ac6` rename UserController.php to ProfileController.php (@tomcdj71)
- `889bff8` set  when activating an user (@tomcdj71)
- `6a5fb57` ensure zen scrips are ran into the proper way (@tomcdj71)
- `ba1ce33` mark FeatureFlag Commands as final classes (@tomcdj71)
- `cac483f` move regenerateX commands to App\Commands\Secrets namespace (@tomcdj71)
- `b59ed65` reorganize template files (@tomcdj71)
- `3a9cb79` use rector on Exception/Error classes (@tomcdj71)
- `d6c62cf` add a Preference settings for enabling verbosity mode when installing apps (@tomcdj71)
- `2bfb0e2` change namespace of GroupController and SettingsController (@tomcdj71)
- `551a1a5` remove deprecation from EmailVerifier (@tomcdj71)
- `e0e2ffa` re-organize Controller namespaces (@tomcdj71)
- `32aa7c6` add a TimestampableTrait for later timestamps implementations (@tomcdj71)
- `0602135` move reset password forms to App\Form\User\Reset (@tomcdj71)
- `17afa0a` remove 'final' from entities to prevent lazyghost errors (@tomcdj71)
- `9c771ab` remove unused export (@tomcdj71)
- `a09b589` add  and activatedAt properties to the User entity (@tomcdj71)
- `29712a1` add Preference->primaryMountPath() property (@tomcdj71)
- `61129c1` add __toString magic method in Group and Mount entities (@tomcdj71)
- `2fd1a86` edit some user preferences naming for clarity (@tomcdj71)
- `8989187` add new image fields for a better customization (@tomcdj71)
- `69b00f6` ensure uploaded user images are ignored (@tomcdj71)
- `666b7fc` update .gitignore (@tomcdj71)
- `23df705` rename ImageUploaderService to Image/UploadImageService (@tomcdj71)
- `4126ae0` change avatar and background paths (@tomcdj71)
- `7a7ee96` redirect to home if an user tries to access login route (@tomcdj71)
- `4160475` ensure only editable groups are editable (@tomcdj71)
- `3070c33` add new repository functions (@tomcdj71)
- `008b0bd` add new entity properties (@tomcdj71)
- `98fec52` use chosen backdrops (@tomcdj71)
- `00e3bd5` minor changes to serialization groups (@tomcdj71)
- `1ec4fb4` reload php after changing a php setting (@tomcdj71)
- `9f0d303` update .gitignore (@tomcdj71)
- `9223fea` ensure .env variables are accessible (@tomcdj71)
- `aa77166` update regenerate commands namespace (@tomcdj71)
- `11d86b3` replace FormType to Type (@tomcdj71)
- `bb77051` update RegistrationFormType to RegistrationType (@tomcdj71)
- `f78f966` page title now uses definied SITE_NAME (@tomcdj71)
- `ca1a054` minor edit to base templates (@tomcdj71)
- `ad9b5fe` use new table_of_content macro (@tomcdj71)
- `6dafecd` add the subdomain in the path for later tests (@tomcdj71)
- `00524cf` enable /settings/general route (@tomcdj71)
- `380a314` remove unused imports (@tomcdj71)
- `92dc0e6` remove secrets from the global .env file (@tomcdj71)
- `93620fb` minor edits to template files (@tomcdj71)
- `dfc6f69` add pins_controller.js (@tomcdj71)
- `2bd6d86` add mercure configuration (@tomcdj71)
- `678e086` add package-lock to .gitignore (@tomcdj71)
- `22403e8` use pnpm instead npm in make and justfile (@tomcdj71)
- `319c3bb` wip - add AppPinUI.js (@tomcdj71)
- `e84688f` rename AppButtons to ButtonGenerator (@tomcdj71)
- `59574e9` remove migrations file until the end of development (@tomcdj71)
- `e7a7ec6` changes to entities (@tomcdj71)

## 👷 Continuous Integration

- `d7d73d4` refactor ci files to use python scripts from the workflow repo (@tomcdj71)
- `679ddec` update workflow (@tomcdj71)
- `a10c963` fix harmonyui path (@tomcdj71)
- `f14a7c3` fix bad spelling (@tomcdj71)
- `700163a` change step naming (@tomcdj71)
- `42ed287` fix resource name (@tomcdj71)
- `7b3ab01` update git token name for consistency across repos (@tomcdj71)
- `d3c6673` update to repository-dispatch@v3 (@tomcdj71)
- `7f10a75` better release process (@tomcdj71)
- `4662b54` better release generation (@tomcdj71)
- `aeb2fea` update workflow (#1) (@tomcdj71)
- `fe725ee` update workflow (@tomcdj71)

## 🔧 Configuration

- `7db1e58` properly setup ux-turbo (@tomcdj71)
- `057b81e` add daisyui configuration in tailwind.config.js (@tomcdj71)
- `8de8880` add ux_icon configuration file (@tomcdj71)
- `3026c3d` enhance twig configuration (@tomcdj71)
- `61b4346` configure liip/imagine-bundle to have a set of pre-built filters (@tomcdj71)

## 📝 Documentation

- `5ddca18` add docblocks to the NewAppForm.js file (@tomcdj71)
- `a0e84e8` add missing copyright notice (@tomcdj71)
- `407701d` update copyright notice (@tomcdj71)
- `8e60afb` add docblocks to the PinService (@tomcdj71)
- `689ef78` update DotenvUpdater docblocks (@tomcdj71)
- `79a33ef` update docs for the AppStore module (@tomcdj71)
- `d215669` update PR template (@tomcdj71)

## 🌐 Internationalization

- `007d357` add more translatable strings (@tomcdj71)
- `ed873eb` remove unused translations (@tomcdj71)
- `d21bd7b` AppStore is now translatable (@tomcdj71)
- `605ae86` add new translations keys (@tomcdj71)
- `fffa37e` setup translations (@tomcdj71)

## 🤷 Other Changes

- `a76ffa5` remove openapi-redoc.yaml (@tomcdj71)

## ⚡ Performance Improvements

- `00d4a5d` ensure there is only 1 request done for pin action (@tomcdj71)

## ♻️ Refactors

- `fbe8d27` install process (@tomcdj71)
- `ebb4075` api specifications (@tomcdj71)
- `be0bfb6` ResetDefaultImageService (@tomcdj71)
- `fa13f4d` PHP form according to new php.ini template (@tomcdj71)
- `a7780a5` Controllers (@tomcdj71)
- `cd78203` PreferenceController (@tomcdj71)
- `394240d` avoid code duplication on settings_sidebar (@tomcdj71)
- `fdaa9c4` table_of_contents macro (@tomcdj71)
- `840c601` pinned apps are now using stimulus (@tomcdj71)
- `c8e1231` app management (@tomcdj71)
- `87d52b2` App Table (@tomcdj71)
- `7c15ad5` App Cards (@tomcdj71)
- `0fad608` AppStore.js into AppStoreManager.js and AppStoreUI.js for SRP adherence (@tomcdj71)

## ⏪️ Reverts

- `bb77964` correcctly bypass invalid app config.yaml if requested (@tomcdj71)
- `8462fa8` remove the ability to register/create an account for now (@tomcdj71)

## 🔒 Security

- `72db970` remove nelmio_security deprecations (@tomcdj71)
- `9fab47a` add better serialization groups on entities (@tomcdj71)

## 💄 Code Style

- `5fc2561` minor code styling edit (rector) (@tomcdj71)
- `31a6d9a` use Rector (@tomcdj71)
- `34c0d6e` minor changes from rector (@tomcdj71)
- `6ec4331` rename functions of FormHandler (@tomcdj71)
- `434d6a5` add interfaces for giving context to services (@tomcdj71)
- `71222cc` minor code style edition (@tomcdj71)
- `27f6c15` move {Type}SettingsType forms to /Setting/{Type}Type for better namespacing (@tomcdj71)
- `f14ef46` lint files (@tomcdj71)
- `2c2bc10` use rector and phpcs (@tomcdj71)
- `29afaee` use rector and phpcs (@tomcdj71)
- `dd9e64c` use rector and phpcs (@tomcdj71)
- `8d7a346` add docblocks for the OptionsMenu (@tomcdj71)

## ✅ Tests

- `8ec1e89` use strict boolean is UserFixtures (@tomcdj71)
- `71e41bd` use new properties in UserFixtures (@tomcdj71)

## 🎨 UI/UX

- `1538eb6` translate the Custom App Wizard (@tomcdj71)
- `e0e474d` (form/macros) - fix save button invocation (@tomcdj71)
- `85d952d` (form/dropzone) - conditionally change style if there is no source image to display (@tomcdj71)
- `df79e00` (App Store) add documentation links for each apps (@tomcdj71)
- `7a85538` add a tooltip when hovering a disabled app (@tomcdj71)
- `256f93d` better blur effects on disabled apps (@tomcdj71)
- `77a3919` (App Store) add tooltip when hovering a Pro app (@tomcdj71)
- `88ddea6` add new search field for the Card view (@tomcdj71)
- `bfb4c49` remove Search button from the App Finder field - now live search is enabled (@tomcdj71)
- `5829bce` redesign main app finder (@tomcdj71)
- `3784120` (App Store) - align buttons no matter of the app descriptions (@tomcdj71)
- `7373e15` (App Store) - rename 'Home' category to 'Discover' (@tomcdj71)
- `7e1cae9` (App Store) - add a quick search function (@tomcdj71)
- `0bbb9d4` (App Store) - add a pro badge for pro apps (@tomcdj71)
- `8c46518` ensure Toggle is in the correct state when loading the page (@tomcdj71)
- `4c91dad` only see one icon in toggle switches (@tomcdj71)
- `0e31206` change registration flow to remove unnecesssary complexity (@tomcdj71)
- `99c678a` enhance style of reset_password pages (@tomcdj71)
- `619b7bf` conditionally check and hide buttons to edit or ban/users (@tomcdj71)
- `5553b6f` add section to quickly display pending user activations (@tomcdj71)
- `0d9daec` get output on login screen if a banned user attempts to log in (@tomcdj71)
- `253dcbd` add registration IP output inside the Profile's Account Informations (@tomcdj71)
- `7bdcf78` add the Toast notification where they are needed (@tomcdj71)
- `0ff855e` add a Toast component (@tomcdj71)
- `7598b92` minor styling edit (@tomcdj71)
- `2cd8969` disable mount path and export data features until ready (@tomcdj71)
- `a6ce6f4` (macros) refactor the badge_macro for a better use in an array context (@tomcdj71)
- `6900486` change style of the closing modale button (@tomcdj71)
- `f632c54` remove unneeded import (@tomcdj71)
- `198c9f6` (App Store) better theming (@tomcdj71)
- `bd677c9` (App Store) minor theming tweaks (@tomcdj71)
- `8b4165c` (AppStore) ensure Explore category always display 6 apps, no matter the user group is (@tomcdj71)
- `db83a8c` (AppStore) disable the navigation links if user have not the permissions to access to (@tomcdj71)
- `73d64e6` better filtering when =false (@tomcdj71)
- `3e96294` (App Store) terminal output styling now finished (@tomcdj71)
- `503a48a` change wording for the Search field (@tomcdj71)
- `016e2c9` (App Store) add icons next to category links (@tomcdj71)
- `7a9288b` change 'external link' icon size (@tomcdj71)
- `ab5976c` better ui for the Transcoding page (@tomcdj71)
- `2933f74` don't display the delete button for the primary mount path (@tomcdj71)
- `3304c48` (profile) display a confirmation message when deleting a mount path (@tomcdj71)
- `4a8478a` change infobox icons (@tomcdj71)
- `180bd40` add a toggle switch button instead of classic checkbox (@tomcdj71)
- `957c0f5` update Form buttons to use symfony/ux-icon resolver instead old resolver (@tomcdj71)
- `b0a0b27` add the 'reset-image' links to restore avatar or background from the profile page (@tomcdj71)
- `b3aef8f` extends profile macros to be more flexible (@tomcdj71)
- `e2f1414` fix spacing on splashscreen to avoid unwanted scroll (@tomcdj71)
- `fbf203a` ensure brand_icon_macro is used accross all base templates (@tomcdj71)
- `5553fda` use the custom branding in templates (@tomcdj71)
- `cc1900f` create brand_icon_macro for custom branding (@tomcdj71)
- `433a433` better logic on backdrop retrieval (@tomcdj71)
- `369469b` refactor profile macros (@tomcdj71)
- `5a0c2e7` add profile page (@tomcdj71)
- `0a5cfb1` use a macro for retrieving users backdrop (@tomcdj71)
- `d327cf4` change the save button classes (@tomcdj71)
- `95c68ee` disable the edition of the force_extra_parameters setting (@tomcdj71)
- `601e6be` standardize template colors (@tomcdj71)
- `4ca9e93` hide contextual menu links if needed (@tomcdj71)
- `879d31a` add external icon on infobox links (@tomcdj71)
- `69f038e` fix tooltip alignment (@tomcdj71)
- `1236071` create breadcrumb component for settings pages (@tomcdj71)
- `1987120` create infobox component for settings page (@tomcdj71)
- `e9eced0` add help tooltips on general settings (@tomcdj71)
- `e7d3508` add new form macros (@tomcdj71)
- `f77d778` enhance php settings (@tomcdj71)
- `03c1e97` add a context notion in the form macros to fine tune translations keys (@tomcdj71)
- `c8029da` add info box to explain the purpose of this form (@tomcdj71)
- `8de2c93` detect network interfaces for the network interface setting (@tomcdj71)
- `fbf79d0` minor twig edits (@tomcdj71)
- `dc9c716` add docs link on settings title (@tomcdj71)
- `f612774` add tooltip on external sidebar links (@tomcdj71)
- `a818bae` fix background size on main dashboard (@tomcdj71)
- `c396705` hide unneeded link for now (@tomcdj71)
- `696e633` background is now full screen (@tomcdj71)
- `33a6062` use twig macros for form fields (@tomcdj71)
- `f53fc1a` set current page/tab link color (@tomcdj71)
- `2f1fbfa` edit page styling (@tomcdj71)
- `a362260` ensure background is fullscreen (@tomcdj71)
- `e6ff6e2` adjust widgets styling (@tomcdj71)
- `2e17ca5` remove AppStore from the app list (@tomcdj71)
- `dd3a76d` pinned apps now displayed in the navbar (@tomcdj71)

## Other Changes

- `33b63f5` remove soft_logos path as it will be included into zen instead (@tomcdj71)
- `80c5a6c` remove old 'App Store' app mentions from the code (@tomcdj71)
- `a9be7d5` add Validation module for better form error display (@tomcdj71)
- `61643a8` add  endpoint for the logs page specific assets (@tomcdj71)
- `93940f4` update composer and pnpm dependencies (@tomcdj71)
- `b684430` adjust phpinsights config (@tomcdj71)
- `8235e87` fix just qa-lint command (@tomcdj71)
- `bb4d9bc` feature pin app to navbar (#3) (@tomcdj71)

**Full Changelog**: https://github.com/MediaEase/HarmonyUI/compare/v0.0.1...0.51.14-alpha