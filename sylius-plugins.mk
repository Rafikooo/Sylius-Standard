SUPPORTED_PLUGINS = \
  marketplace-plugin

.PHONY: list-sylius-plugins
list-sylius-plugins:
	@echo -e "\033[1;32mAvailable Sylius Plugins:\033[0m"
	@echo "$(SUPPORTED_PLUGINS)" | tr ' ' '\n'

.PHONY: install-sylius-plugin
install-sylius-plugin:
	@if [ -z "$(PLUGIN)" ]; then \
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
	vendor/bin/rector process src || \
	  (echo -e "\033[1;31mError: Rector process failed.\033[0m"; exit 1); \
	vendor/bin/rector process src || \
	  (echo -e "\033[1;31mError: Rector process failed.\033[0m"; exit 1); \
	echo -e "\033[1;32mRector process completed successfully.\033[0m"
