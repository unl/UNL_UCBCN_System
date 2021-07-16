SHELL := /bin/bash
CURL := curl

# NodeJS Find/Install
NODE_PATH = $(shell ./find-node-or-install)
PATH := $(NODE_PATH):$(shell echo $$PATH)

# External Build Tools
NODE_DIR = node_modules
LESSC = $(NODE_DIR)/less/bin/lessc
UGLIFYJS = $(NODE_DIR)/uglify-js/bin/uglifyjs

# Local Vars
LESS_LIB = www/templates/default/html/less/lib

# External Dependencies
LESSHAT := $(LESS_LIB)/lesshat.less
WDN_MIXINS := \
	$(LESS_LIB)/breakpoints.less \
	$(LESS_LIB)/colors.less \
	$(LESS_LIB)/fonts.less

WDN_LIB_RAW = https://raw.githubusercontent.com/unl/wdntemplates/master/wdn/templates_4.1/less/_mixins/
LESSHAT_RAW = https://raw.githubusercontent.com/csshat/lesshat/c8c211b2442734bfc1ae2509ff0ccebdc2e73664/build/lesshat.less

# Built Files
CSS_OBJ = www/templates/default/html/css/events.css
CSS_OBJ2 = www/templates/default/html/css/manager.css
JS_OBJ = www/templates/default/html/js/events.min.js
JS_OBJ2 = www/templates/default/html/js/manager.min.js
JS_OBJ3 = www/templates/default/html/js/event-image.min.js

all: less js

less: $(CSS_OBJ) $(CSS_OBJ2)

js: $(JS_OBJ) $(JS_OBJ2) $(JS_OBJ3)

clean:
	rm -r $(NODE_DIR)
	rm $(JS_OBJ) $(JS_OBJ2) $(JS_OBJ3)
	rm $(CSS_OBJ)
	
$(CSS_OBJ): www/templates/default/html/less/events.less www/templates/default/html/less/eventicon-embedded.less $(LESSC) $(LESSHAT) $(WDN_MIXINS)
	$(LESSC) --clean-css $< $@

$(CSS_OBJ2): www/templates/default/html/less/manager.less www/templates/default/html/less/eventicon-embedded.less $(LESSC) $(LESSHAT) $(WDN_MIXINS)
	$(LESSC) --clean-css $< $@
	
$(LESSC):
	npm install less

$(LESS_LIB)/%.less:
	@mkdir -p $(@D)
	$(CURL) -s $(WDN_LIB_RAW)$(@F) -o $@

$(LESSHAT):
	@mkdir -p $(@D)
	$(CURL) -s $(LESSHAT_RAW) -o $@
	
$(UGLIFYJS):
	npm install uglify-js
	
$(JS_OBJ): www/templates/default/html/js/events.js $(UGLIFYJS)
	$(UGLIFYJS) $< -c -m -o $@ --source-map

$(JS_OBJ2): www/templates/default/html/js/manager.js $(UGLIFYJS)
	$(UGLIFYJS) $< -c -m -o $@ --source-map

$(JS_OBJ3): www/templates/default/html/js/event-image.js $(UGLIFYJS)
	$(UGLIFYJS) $< -c -m -o $@ --source-map

.PHONY: all less js clean
