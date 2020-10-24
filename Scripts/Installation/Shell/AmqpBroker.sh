#!/bin/bash
read -p "? This script will install the iotJumpWay AMQP broker on your HIAS Server. Are you ready (y/n)? " cmsg

if [ "$cmsg" = "Y" -o "$cmsg" = "y" ]; then
	echo "- Installing iotJumpWay AMQP Broker...."
	sudo tee /etc/apt/sources.list.d/bintray.rabbitmq.list <<-EOF
	deb https://dl.bintray.com/rabbitmq-erlang/debian bionic erlang
	deb https://dl.bintray.com/rabbitmq/debian bionic main
	EOF
	sudo apt-get update -y
	sudo apt-get install -y rabbitmq-server
	sudo touch /etc/rabbitmq/rabbitmq.config
	pip3 install pika
	sudo wget https://dl.bintray.com/rabbitmq/community-plugins/rabbitmq_auth_backend_http-3.6.x-61ed0a93.ez -P /usr/lib/rabbitmq/lib/rabbitmq_server-3.6.10/plugins
	sudo wget http://www.rabbitmq.com/releases/plugins/v2.4.1/mochiweb-2.4.1.ez -P /usr/lib/rabbitmq/lib/rabbitmq_server-3.6.10/plugins
	sudo rabbitmq-plugins enable rabbitmq_auth_backend_http
	sudo rabbitmq-plugins enable rabbitmq_management
	pip3 install gevent
	sudo touch /etc/rabbitmq/rabbitmq.config
	read -p "? Enter your HIAS server URL (Without https://): " domain
	echo "%% -*- mode: erlang -*-" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "%% ----------------------------------------------------------------------------" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "%% Classic RabbitMQ configuration format example." | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "%% This format should be considered DEPRECATED." | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "%%" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "%% Users of RabbitMQ 3.7.x" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "%% or later should prefer the new style format (rabbitmq.conf)" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "%% in combination with an advanced.config file (as needed)." | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "%%" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "%% Related doc guide: https://www.rabbitmq.com/configure.html. See" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "%% https://rabbitmq.com/documentation.html for documentation ToC." | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "%% ----------------------------------------------------------------------------" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "[" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo " {rabbit," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "  [" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "   {tcp_listeners, []}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "   {ssl_listeners, [5671]}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "   {ssl_options, [{cacertfile,\"/fserver/certs/fullchain.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                  {certfile,\"/fserver/certs/cert.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                  {keyfile,\"/fserver/certs/privkey.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                  {verify,verify_peer}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                  {fail_if_no_peer_cert,false}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                  {versions,['tlsv1.2']}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                  {server_name_indication, \"$domain\"}]}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "   {auth_backends, [rabbit_auth_backend_http]}]}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "    {rabbitmq_auth_backend_http," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "     [{http_method,   post}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "      {user_path,     \"https://$domain/iotJumpWay/AMQP/API/User\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "      {vhost_path,     \"https://$domain/iotJumpWay/AMQP/API/Vhost\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "      {resource_path,     \"https://$domain/iotJumpWay/AMQP/API/Resource\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "      {topic_path,     \"https://$domain/iotJumpWay/AMQP/API/Topic\"}" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "  ]}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "  {rabbitmq_management," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "   [" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "    {listener, [{port,     15671}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "     {ssl,      true}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "     {ssl_opts, [{cacertfile, \"/fserver/certs/fullchain.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                 {certfile, \"/fserver/certs/cert.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                 {keyfile, \"/fserver/certs/privkey.pem\"}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                 {verify, verify_peer}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                 {fail_if_no_peer_cert, false}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                 {client_renegotiation, false}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                 {secure_renegotiate,   true}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                 {honor_ecc_order,      true}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                 {honor_cipher_order,   true}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                 {versions,['tlsv1.1', 'tlsv1.2']}," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                 {ciphers, [\"ECDHE-ECDSA-AES256-GCM-SHA384\"," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                            \"ECDHE-RSA-AES256-GCM-SHA384\"," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                            \"ECDH-ECDSA-AES256-GCM-SHA384\"," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                            \"ECDH-RSA-AES256-GCM-SHA384\"," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                            \"ECDH-RSA-AES256-SHA384\"," | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                            \"DHE-RSA-AES256-GCM-SHA384\"" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "                            ]}" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "               ]}" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "             ]}" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "    ]}" | sudo tee -a /etc/rabbitmq/rabbitmq.config
	echo "]." | sudo tee -a /etc/rabbitmq/rabbitmq.config
	sudo systemctl restart rabbitmq-server
	echo "- Installed iotJumpWay AMQP Broker!";
	exit 0
else
	echo "- iotJumpWay AMQP Broker installation terminated!";
	exit 1
fi