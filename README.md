# HAWKI

# NEU NEU NEU

Im Rahmen der gemeinsamen Weiterentwicklung von HAWKI möchten wir ein Dokument zur Verfügung stellen, das verschiedene Nutzungsmöglichkeiten von HAWKI aufzeigt. Hier ist Platz, um Promptvorschläge für die Hochschullehre zu machen oder weitere fiktive Expert*innen für das virtuelle Büro hinzuzufügen.
https://pad.hawk.de/p/Offener_Prompt-Katalog

### How to start local development?

1. ##### Setup a proxy with nginx (which includes docker-gen)
   Place the following ```docker-compose.yml``` in the folder of your choice, e.g. ```/var/apps/nginx-proxy/``` or ```/<your-home-dir>/workspace/nginx-proxy/```.
    ```yaml
    version: "3.4"
   
    services:
        nginx-proxy:
            image: jwilder/nginx-proxy:alpine
            container_name: nginx-proxy
            ports:
                - target: 80
                  published: 80
                  protocol: tcp
                - target: 443
                  published: 443
                  protocol: tcp
            volumes:
                - /var/run/docker.sock:/tmp/docker.sock:ro
                - ./certs:/etc/nginx/certs
                - ./vhosts:/etc/nginx/conf.d
            networks:
                - hawki
    
    networks:
        hawki:
           external: true
    ```

2. ##### Install local ssl certificates
   The following could be automated with own Dockerfile
   including jwilder/nginx-proxy and mkCert - alike to dev-tls docker file. <br>

   First you have to settle on a domain to use for hawki.
   We will use `hawki.local` in the following.
   Using only `hawki` would be troubling for certificates since most browsers
   do not accept wildcard certificates for second-level domains:
   e.g. browser will not accept certs created with `*.hawki` for subdomain `api.hawki`

   ###### install [mkCert](https://github.com/FiloSottile/mkcert)
    ```BASH
    # linux
    wget https://github.com/FiloSottile/mkcert/releases/download/v1.4.1/mkcert-v1.4.1-linux-amd64
    sudo mv mkcert-v1.4.1-linux-amd64 /usr/local/bin/mkcert
    sudo chmod +x /usr/local/bin/mkcert
    ```
    ```BASH
    # macOS
    brew install mkcert
    brew install nss # if you use Firefox
    ```
   ###### Install rootCA
   This automatically adds mkcert's rootCA to your systems trusted CAs so you no longer will be bugged by untrusted certificate notifications in your local browser.
    ```BASH
    mkcert --install
    ```
###### create Certs
Navigate to the nginx-proxy certs volume e.g. `cd /var/apps/nginx-proxy/certs` or ```/<your-home-dir>/workspace/nginx-proxy/certs```
   ```BASH
   mkcert -key-file hawki.local.key -cert-file hawki.local.crt hawki.local *.hawki.local  
   ```
This generates a certificate for all subdomains of `hawki.local`

3. ##### setup dns
    ```BASH
    # /etc/hosts
    127.0.0.1	hawki.local
    ```

4. ##### start nginx-proxy

    ```BASH
    # this need to be run only once 
    docker network create hawki
    ```

    ```BASH
    # /var/apps/nginx-proxy/ or /<your-home-dir>/workspace/nginx-proxy/
    docker-compose up -d    
    ```
5. ##### create .env
   ```BASH
   #<yourWorkSpace>/hawki
   cp .env.dist .env
   ```
   

6. ##### start hawki stack
    ```BASH
    #<yourWorkSpace>/hawki
    docker-compose up -d
    ```
   nginx-proxy will create a vhost entry for each of hawki's services which has an environment variable `VIRTUAL_HOST` set.
   You can check the created hosts in a volume:

    ```BASH
    cat path-to-nginx-proxy/vhosts/default.conf
    ```

## About

HAWKI is a didactic interface for universities based on the OpenAI API. It is not necessary for users to create an account, the university ID is sufficient for login - no user-related data is stored.

The service was developed by Jonas Trippler, Vincent Timm and Stefan Woelwer at the Interaction Design Lab at the HAWK University of Applied Sciences and Arts in order to give all members of the university the opportunity to integrate artificial intelligence into their work processes and to have a meeting place where new ways of working may emerge and an internal university discussion about the use of AI can take place. The interface is currently divided into three areas:

Conversation: A chat area similar to ChatGPT, for a quick start to any task.

Virtual office: Conversations with fictional experts as a mental model to familiarise yourself with non-technical areas and to make more targeted enquiries to real university experts.

Learning Space: The learning spaces are designed to help you understand the different support options and learn what makes an effective prompt.

We welcome constructive feedback to further develop this project based on your needs and insights.

![HAWKI Login](/img/hawki-screenshot-login.png)
_HAWKI Login Screen_

![HAWKI Dashboard](/img/hawki-screenshot-dashboard.jpg)
_HAWKI Dashboard_

## ChangeLog 23.01.2024

Quality of Life Features:

- Message Inputfield scroll panel added
It is now possible to scroll in the text input field. Previously, long text entries were too inconvenient.
- Autoscroll function adjusted. Scroll up stops the auto scroll.
When a response is generated, the user can still scroll up and read the text that has already been generated.
- Stop Generating function added. During the generation process “send” button switches to “stop generation” button.
Now users no longer have to wait until the end of the generation, but can end the process manually.
- Copy Button added. The function copies the whole message as plain text.
Users can use the Copy button to copy the text without formatting. This simplifies the further processing of the generated answers.


Bugfix
- Parsing error from json "Chunks" corrected (merged code from Uni Kassel / thx to Niklas Wode).
Previously, the response was sometimes not generated completely or contained errors.

Other

- Removed testing files
Redundant files from the development phase

- Removed docker container 
We cannot offer long-term support for a docker integration and find the setup process simple enough and have therefore removed the docker container.

- Changed standard model to GPT-4-Turbo
At times we had a model switcher built in, but this has now become unnecessary. We have removed the model switcher and set gpt 4 turbo as the standard model.

- Previously, the generated text that was in double asterisks was deleted, now we make it available as bold text, as intended.

## Getting started

## Prequisites

### LDAP

HAWKI uses LDAP under the hood in order to authenticate users. Make sure you have LDAP setup first and that it is accessible from your HAWKI instance. Provide your LDAP config according to chapter [Configuration](#configuration). You can find more information on how to use LDAP on the official website https://ldap.com

_**Testing without LDAP:**_ You can try out HAWKI without an LDAP server. To do so, set `TESTUSER` to `true` in the configuration file (see [Configuration](#configuration)) and sign in with username `tester` and `superlangespasswort123`

### OpenID Connect

As an alternative to LDAP, OpenID connect can also be used to 
authenticate users. It requires the jumbojett/openid-connect-php
library (https://github.com/jumbojett/OpenID-Connect-PHP)
to be installed with composer.

### Open AI Access

To generate answers HAWKI uses the Open AI api. Follow the instructions on https://platform.openai.com/docs/introduction to generate an API key and paste it in the configuration file like instructed in chapter [Configuration](#configuration).

## Configuration

To get started you need to add a configuration file to the project first. Copy the file ".env.example" from the root directory and rename it to ".env". Replace the example values in it with your own configuration. A detailed description of all values is listed below.

| Value            | Type    | Example                                | Description                                                                                                                                        |
| ---------------- | ------- | -------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------- |
| Authentication   | string  | 'LDAP' or 'OIDC'                        | Authentication method: LDAP or OpenID Connect                                                                                                      |
| LDAP_HOST        | string  | "ldaps://...de"                        | The URL of your LDAP server.                                                                                                                       |
| LDAP_BIND_PW     | string  | secretpassword                         | Password of the user that is trying to bind to the LDAP Server.                                                                                    |
| LDAP_BASE_DN     | string  | "cn=...,ou=...,dc=..."                 | Distinguised name that is used to initially bind to your LDAP server.                                                                              |
| LDAP_SEARCH_DN   | string  | "ou=...,dc=..."                        | Distinguished name that is used for authenticating users.                                                                                          |
| OIDC_IDP          | string  | "https://...."                         | URL of the Identity provider supporting OpenID Connect.                                                                                            |
| OIDC_CLIENT_ID    | string  | "..."                                  | Client Id for this application in Identity provider.                                                                                               |
| OIDC_CLIENT_SECRET | string  | "..."                                 | Secret key for OpenID Connect. 
| OIDC_LOGOUT_URI | string  | "https://...."                                 | URL to logout from Identity provider                                                                                                                  |
| OPENAI_API_KEY   | string  | sk-...                                 | Open AI Api key                                                                                                                                    |
| IMPRINT_LOCATION | string  | https://your-university/imprint        | A link to your imprint. Alternatively you can replace the file index.php under /impressum with your own html/ php of your imprint.                 |
| PRIVACY_LOCATION | string  | https://your-university/privacy-policy | A link to your privacy policy. Alternatively you can replace the file index.php under /datenschutz with your own html/ php of your privacy policy. |
| TESTUSER         | boolean | `false`                                | Can be set to `true` for testing purposes. You can then sign in using username `tester` and password `superlangespasswort123`                      |
| FAVICON_URI  | string  | "https://...."                                 | Link to favicon 

## Web Server Configuration

There are a few things to keep in mind when publishing your HAWKI instance on a webserver.

First and foremost your webserver needs PHP support.

Also, make sure that you disable `output_buffering` in your webserver configuration otherwise you might run into issues when receiving the repsonse stream from Open AI.

If you are setting up a new server, make sure that you install the cURL library. https://www.php.net/manual/de/book.curl.php

**_IMPORTANT:_** _Keep the `.env` configuration file secret. Make sure your webserver does not allow directory listing and it blocks access to this configuration file. Double check that it can not be queried with a simple GET request via http://your-hawki-domain/.env_

## Branding

To swap out the HAWK logo for your own, replace the logo.svg file inside the `img` folder. Make sure to either keep the format as svg or replace all references to logo.svg with your respective filetype.

Of course, you can modify stylesheets and html files and adjust them to your liking.

## Contact & License

This project is licensed under the terms of the MIT license. If you have any questions, feel free to get in touch via [Email](mailto:vincent.timm2@hawk.de)
