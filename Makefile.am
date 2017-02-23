CC = @CC@
prefix=@prefix@
SHELL = /bin/sh
exec_prefix=@exec_prefix@
www_prefix=@ac_www@
sbindir=@sbindir@
wwwdir=$(www_prefix)/htdocs
cgibindir=$(www_prefix)/cgi-bin
bindir=@bindir@
CREDITS = credits.php
READ_VERSION = cat $(CREDITS) | grep -e "\"RELEASE_VERSION\"" | sed "s/^.*\"\([[:digit:]]*\.[[:digit:]]*\)\".*/\1/" > VERSION
PROJECT = php-shtooka-get
VERSION = `cat $(PROJECT)/VERSION`
PACKAGE = $(PROJECT)-$(VERSION)
CFLAGS = @CFLAGS@ @DEFS@ -DVERSION=\"$(VERSION)\" -Wall
LDFLAGS = @LDFLAGS@
LIBS = @LIBS@
rc_done = 0

default:  install

clean :
	find . -name "*~" -exec rm "{}" \;
	rm -rf data/*

mrproper: clean
	rm -rf config.status config.log config.cache Makefile autom4te.cache

version : credits.php
	$(READ_VERSION)

tarball: mrproper version
	cd .. &&rm -f $(PACKAGE)&&ln -s $(PROJECT) $(PACKAGE) &&tar -czvpf $(PACKAGE).tar.gz -h $(PACKAGE)/ 2>&1 || exit 0 && rm $(PACKAGE) && echo "Archive complete (see ../$(PACKAGE).tar.gz)"

install :
	@echo "Installing into $(wwwdir)/$(PROJECT)"
	mkdir -p "$(wwwdir)/$(PROJECT)"
	cp *.php "$(wwwdir)/$(PROJECT)"
	cp -r data "$(wwwdir)/$(PROJECT)"
	chown -R www-data.www-data "$(wwwdir)/$(PROJECT)/data"
	chmod -R 775 "$(wwwdir)/$(PROJECT)/data"
	cp -r lib "$(wwwdir)/$(PROJECT)"
	@echo "Installation done"
