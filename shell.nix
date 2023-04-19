{ pkgs ? import <nixpkgs> { } }:

let
  php = pkgs.php82.buildEnv {
    extensions = { enabled, all }: enabled ++ (with all; [
      xdebug
    ]);
    extraConfig = ''
      xdebug.mode = debug
      memory_limit = 4G
    '';
  };
  inherit (php.packages) composer;

  projectInstall = pkgs.writeShellApplication {
    name = "project-install";
    runtimeInputs = [
      php
      composer
    ];
    text = ''
      rm -rf vendor/ .Build/
      composer install --prefer-dist --no-progress --working-dir="$PROJECT_ROOT"
    '';
  };

  projectValidateComposer = pkgs.writeShellApplication {
    name = "project-validate-composer";
    runtimeInputs = [
      php
      composer
    ];
    text = ''
      composer validate
    '';
  };

in pkgs.mkShell {
  name = "TYPO3 Extension Watchlist";
  buildInputs = [
    projectInstall
    projectValidateComposer
    php
    composer
  ];

  shellHook = ''
    export PROJECT_ROOT="$(pwd)"

    export typo3DatabaseDriver=pdo_sqlite
  '';
}
