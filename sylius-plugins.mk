SUPPORTED_PLUGINS = \
  marketplace-plugin

.PHONY: install-sylius-plugin
install-sylius-plugin:
	@set +x; \
	if [ -z "$(PLUGIN)" ]; then \
	  if ! command -v fzf >/dev/null 2>&1; then \
	    echo -e "\033[1;31mError: 'fzf' is not installed. Please install it to enable interactive selection.\033[0m"; \
	    exit 1; \
	  fi; \
	  echo -e "\033[1;32mAvailable Sylius Plugins:\033[0m"; \
	  PLUGIN=$$(echo "$(SUPPORTED_PLUGINS)" | tr ' ' '\n' | fzf --prompt="Select a plugin to install: "); \
	  if [ -z "$$PLUGIN" ]; then \
	    echo -e "\033[1;31mNo plugin selected. Aborting.\033[0m"; \
	    exit 1; \
	  fi; \
	  $(MAKE) install-sylius-plugin PLUGIN=$$PLUGIN; \
	  exit 0; \
	fi; \
	if ! echo "$(SUPPORTED_PLUGINS)" | grep -w -q "$(PLUGIN)"; then \
	  echo -e "\033[1;31mError: The selected plugin '$(PLUGIN)' is not supported.\033[0m"; \
	  echo -e "\033[1;32mSupported plugins are:\033[0m"; \
	  echo "$(SUPPORTED_PLUGINS)" | tr ' ' '\n'; \
	  exit 1; \
	fi; \
	SYLIUS_PACKAGIST_TOKEN=$$(composer config --global --auth http-basic.sylius.repo.packagist.com.password 2>/dev/null || echo ""); \
	if [ -z "$$SYLIUS_PACKAGIST_TOKEN" ]; then \
	  echo -e "\033[1;33mNo SYLIUS_PACKAGIST_TOKEN found in Composer configuration.\033[0m"; \
	  read -p "Enter your Sylius Packagist token: " SYLIUS_PACKAGIST_TOKEN; \
	  if [ -z "$$SYLIUS_PACKAGIST_TOKEN" ]; then \
	    echo -e "\033[1;31mNo token provided. Aborting.\033[0m"; \
	    exit 1; \
	  fi; \
	  composer config --global http-basic.sylius.repo.packagist.com token "$$SYLIUS_PACKAGIST_TOKEN" || \
	    (echo -e "\033[1;31mError: Failed to set the token. Check if the token is valid.\033[0m"; exit 1); \
	fi; \
	echo -e "\033[1;32mValidating token...\033[0m"; \
	curl -sf -u token:$$SYLIUS_PACKAGIST_TOKEN https://sylius.repo.packagist.com/sylius/packages.json > /dev/null || \
	  (echo -e "\033[1;31mError: Invalid token provided. Aborting.\033[0m"; exit 1); \
	echo -e "\033[1;32mConfiguring Sylius repository...\033[0m"; \
	composer config repositories.sylius composer https://sylius.repo.packagist.com/sylius/ || \
	  (echo -e "\033[1;31mError: Failed to configure the Sylius repository.\033[0m"; exit 1); \
	echo -e "\033[1;32mInstalling plugin '$(PLUGIN)'...\033[0m"; \
	composer require $(PLUGIN) --no-scripts --no-interaction || \
	  (echo -e "\033[1;31mError: Failed to install plugin '$(PLUGIN)'.\033[0m"; \
	   echo -e "\033[1;33mCheck the token or plugin name.\033[0m"; \
	   exit 1); \
	echo -e "\033[1;32mPlugin '$(PLUGIN)' installed successfully.\033[0m"; \
	echo -e "\033[1;32mRunning Rector for code cleanup...\033[0m"; \
	vendor/bin/rector process src --no-progress-bar --no-diffs || \
	  (echo -e "\033[1;31mError: Rector process failed.\033[0m"; exit 1); \
	echo -e "\033[1;32mRector process completed successfully.\033[0m"; \

	echo -e "\033[1;32mWarming up Symfony cache...\033[0m"; \
	bin/console cache:warmup || \
	  (echo -e "\033[1;31mError: Cache warmup failed.\033[0m"; exit 1); \
	echo -e "\033[1;32mCache warmed up successfully.\033[0m"; \

	echo -e "\033[1;32mRunning migrations...\033[0m"; \
	bin/console doctrine:migrations:migrate --no-interaction || \
	  (echo -e "\033[1;31mError: Migrations failed.\033[0m"; exit 1); \
	echo -e "\033[1;32mMigrations completed successfully.\033[0m"; \

	echo "Copying required Sylius templates..."

	TEMPLATES="\
		bundles/SyliusAdminBundle/Order/Show/Summary/_totals.html.twig \
		bundles/SyliusAdminBundle/Product/Show/_header.html.twig \
		bundles/SyliusCoreBundle/Email/Blocks/OrderConfirmation/_content.html.twig \
		bundles/SyliusUiBundle/Modal/_confirmation.html.twig \
		bundles/SyliusUiBundle/_flashes.html.twig \
		bundles/SyliusShopBundle/Taxon/_horizontalMenu.html.twig \
		bundles/SyliusShopBundle/Register/_header.html.twig \
		bundles/SyliusShopBundle/ProductReview/create.html.twig \
		bundles/SyliusShopBundle/Product/_box.html.twig \
		bundles/SyliusShopBundle/Product/Show/_reviews.html.twig \
		bundles/SyliusShopBundle/Order/_summary.html.twig \
		bundles/SyliusShopBundle/Common/Form/_login.html.twig \
		bundles/SyliusShopBundle/Checkout/_header.html.twig \
		bundles/SyliusShopBundle/Account/Order/Show/_header.html.twig \
	"

	for file in $$TEMPLATES; do \
		mkdir -p templates/$$(dirname $$file); \
		cp vendor/sylius/plus-marketplace-suite-plugin/templates/$$file templates/$$file; \
	done

	echo "✅ Sylius templates copied successfully."

	echo "DEBUG: Asking user about optional templates..."
	read -p "Do you want to copy optional marketplace templates (replace Sylius names with marketplace branding, update logos, etc.)? (y/n): " CONFIRM_COPY; \
	if [ "$$CONFIRM_COPY" = "y" ]; then \
	  OPTIONAL_TEMPLATES="\
		bundles/SyliusAdminBundle/Layout/_logo.html.twig \
		bundles/SyliusAdminBundle/Layout/_notification.html.twig \
		bundles/SyliusAdminBundle/Security/login.html.twig \
		bundles/SyliusAdminBundle/layout.html.twig \
		bundles/SyliusCoreBundle/Email/layout.html.twig \
		bundles/SyliusUiBundle/Layout/centered.html.twig \
		bundles/SyliusUiBundle/Security/_logo.html.twig \
		bundles/TwigBundle/Exception \
		bundles/SyliusShopBundle/Layout/Header/_logo.html.twig \
		bundles/SyliusShopBundle/Homepage/_banner.html.twig \
	  "; \
	  for file in $$OPTIONAL_TEMPLATES; do \
		mkdir -p templates/$$(dirname $$file); \
		cp vendor/sylius/plus-marketplace-suite-plugin/templates/$$file templates/$$file; \
	  done; \
	  echo "✅ Optional marketplace templates copied successfully."; \
	else \
	  echo "❌ Skipping optional marketplace templates."; \
	fi

	echo -e "\033[1;32mUpdating webpack.config.js to include plugin assets...\033[0m"; \
	if ! grep -q "syliusMarketplaceSuiteShop" webpack.config.js; then \
	  sed -i'' -e "/module.exports = \[/i\const [syliusMarketplaceSuiteShop, syliusMarketplaceSuiteAdmin] = require('./vendor/sylius/plus-marketplace-suite-plugin/webpack.config');" webpack.config.js; \
	  sed -i'' -e "/module.exports = \[/s/\[/[ syliusMarketplaceSuiteShop, syliusMarketplaceSuiteAdmin, /" webpack.config.js; \
	  echo -e "\033[1;32mwebpack.config.js updated successfully.\033[0m"; \
	else \
	  echo -e "\033[1;33mwebpack.config.js already contains the required imports.\033[0m"; \
	fi;
