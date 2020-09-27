pragma solidity ^0.7.1;
pragma experimental ABIEncoderV2;

// SPDX-License-Identifier: MIT

contract HIAS {

	uint compensation = 1000000000000000000;
	address haccount = 0x1F4EFc2329a4047Bf2eCa01bb1d011382B0C355A;
	bool setup = false;

	struct user {
		address bcaddress;
		bool allowed;
		bool admin;
		uint user;
		string name;
		uint location;
		uint application;
		uint created;
		uint createdBy;
		uint updated;
		bool exists;
	}

	struct application {
		address bcaddress;
		bool allowed;
		bool admin;
		uint location;
		uint application;
		string name;
		string status;
		uint created;
		uint createdBy;
		uint updated;
		bool exists;
	}

	struct device {
		address bcaddress;
		uint location;
		uint zone;
		uint device;
		string name;
		string status;
		uint created;
		uint createdBy;
		uint updated;
		bool exists;
	}

	uint applications = 0;
	uint devices = 0;
	uint users = 0;

	mapping(string => application) applicationMap;
	mapping(string => device) deviceMap;
	mapping(string => user) userMap;
	mapping (address => bool) private authorized;
	mapping (address => bool) private admins;

	function isHIAS()
		private
		view
		returns(bool) {
			return msg.sender == haccount;
		}

	function callerAllowed()
		private
		view
		returns(bool) {
			return authorized[msg.sender];
		}

	function callerAdmin()
		private
		view
		returns(bool) {
			return admins[msg.sender];
		}

	function identifierAllowed(string memory _type, string memory identifier)
		public
		view
		returns(bool) {
			require(callerAllowed());
			if(compare(_type, "Application")){
				return applicationMap[identifier].allowed == true;
			} else if(compare(_type, "User")){
				return userMap[identifier].allowed == true;
			} else{
				return false;
			}
		}

	function getBalance()
		public
		view
		returns (uint256) {
			require(isHIAS());
			return address(this).balance;
		}

	function deposit(uint256 amount)
		payable
		public {
			require(isHIAS());
			require(msg.value == amount);
		}

	function updateCompensation(uint amount)
		public {
			require(isHIAS());
			compensation = amount;
		}

	function compensate(address payable _address, uint256 amount)
		private {
			_address.transfer(amount);
		}

	function exists(string memory _type, string memory identifier)
		private
		view
		returns(bool) {
			if(compare(_type, "Application")){
				return applicationMap[identifier].exists;
			} else if(compare(_type, "Device")){
				return deviceMap[identifier].exists;
			} else if(compare(_type, "User")){
				return userMap[identifier].exists;
			} else{
				return false;
			}
		}

	function initiate(string memory _identifier, address _address, bool _admin, uint _user, string memory _name, uint _location, uint _application, uint _createdBy, uint _time)
		public {
			require(isHIAS());
			require(setup == false);
			user memory newUser = user(_address, true, _admin, _user, _name,  _location, _application, _time, _createdBy, _time, true);
			userMap[_identifier] = newUser;
			users++;
			application memory newApplication = application(_address, true, _admin, _location, _application, _name, "OFFLINE", _time, _createdBy, _time, true);
			applicationMap[_identifier] = newApplication;
			applications++;
			authorized[_address] = true;
			if(_admin)
			{
				admins[_address] = true;
			}
			setup = true;
		}

	function registerUser(string memory _identifier, address _address, bool _admin, uint _user, string memory _name, uint _location, uint _application, uint _time, uint _createdBy)
		public {
			require(callerAllowed());
			require(callerAdmin());
			user memory newUser = user(_address, true, _admin, _user, _name,  _location, _application, _time, _createdBy, _time, true);
			userMap[_identifier] = newUser;
			users++;
			if(_admin)
			{
				admins[_address] = true;
			}
			application memory newApplication = application(_address, true, _admin, _location, _application, _name, "OFFLINE", _time, _createdBy, _time, true);
			applicationMap[_identifier] = newApplication;
			applications++;
			authorized[_address] = true;
			compensate(msg.sender, compensation);
		}

	function registerApplication(string memory _identifier, address _address, bool _admin, uint _location, uint _application, string memory _name, uint _createdBy, uint _time)
		public {
			require(callerAllowed());
			require(callerAdmin());
			application memory newApplication = application(_address, true, _admin, _location, _application, _name, "OFFLINE", _time, _createdBy, _time, true);
			applicationMap[_identifier] = newApplication;
			applications++;
			authorized[_address] = true;
			if(_admin)
			{
				admins[_address] = true;
			}
			compensate(msg.sender, compensation);
		}

	function registerDevice(string memory _identifier, address _address, uint _location, uint _zone, uint _device, string memory _name, uint _createdBy, uint _time)
		public {
			require(callerAllowed());
			require(callerAdmin());
			device memory newDevice = device(_address, _location, _zone, _device, _name, "OFFLINE", _time, _createdBy, _time, true);
			deviceMap[_identifier] = newDevice;
			devices++;
			compensate(msg.sender, compensation);
		}

	function updateUser(string memory _identifier, string memory _type, bool _allowed, bool _admin, uint _user, string memory _name, uint _location, uint _application, uint _time)
		public {
			require(callerAllowed());
			require(callerAdmin());
			require(exists(_type, _identifier));
			user storage currentUser = userMap[_identifier];
			currentUser.allowed = _allowed;
			currentUser.admin = _admin;
			currentUser.user = _user;
			currentUser.name = _name;
			currentUser.location = _location;
			currentUser.application = _application;
			currentUser.updated = _time;
			authorized[currentUser.bcaddress] = currentUser.allowed;
			admins[currentUser.bcaddress] = currentUser.admin;
			compensate(msg.sender, compensation);
		}

	function updateApplication(string memory _identifier, string memory _type, bool _allowed, bool _admin, uint _location, string memory _name, string memory _status, uint _time)
		public {
			require(callerAllowed());
			require(callerAdmin());
			require(exists(_type, _identifier));
			application storage currentApplication = applicationMap[_identifier];
			currentApplication.allowed = _allowed;
			currentApplication.admin = _admin;
			currentApplication.location = _location;
			currentApplication.name = _name;
			currentApplication.status = _status;
			currentApplication.updated = _time;
			authorized[currentApplication.bcaddress] = currentApplication.allowed;
			admins[currentApplication.bcaddress] = currentApplication.admin;
			compensate(msg.sender, compensation);
		}

	function updateDevice(string memory _identifier, string memory _type, uint _location, uint _zone, uint _device, string memory _name, string memory _status, uint _time)
		public {
			require(callerAllowed());
			require(callerAdmin());
			require(exists(_type, _identifier));
			device storage currentDevice = deviceMap[_identifier];
			currentDevice.location = _location;
			currentDevice.zone = _zone;
			currentDevice.device = _device;
			currentDevice.name = _name;
			currentDevice.status = _status;
			currentDevice.updated = _time;
			compensate(msg.sender, compensation);
		}

	function count(string memory _type)
		public
		view
		returns (uint){
			require(callerAllowed());
			require(callerAdmin());
			if(compare(_type, "Application")){
				return applications;
			} else if(compare(_type, "Device")){
				return devices;
			} else if(compare(_type, "User")){
				return users;
			}
		}

	function deregsiter(string memory _type, string memory _identifier)
		public {
			require(callerAllowed());
			require(callerAdmin());
			require(exists(_type, _identifier));
			if(compare(_type, "Application")){
				delete authorized[applicationMap[_identifier].bcaddress];
				delete applicationMap[_identifier];
				applications--;
			} else if(compare(_type, "Device")){
				delete authorized[deviceMap[_identifier].bcaddress];
				delete deviceMap[_identifier];
				devices--;
			} else if(compare(_type, "User")){
				delete authorized[userMap[_identifier].bcaddress];
				delete userMap[_identifier];
				users--;
			}
			compensate(msg.sender, compensation);
		}

	function getUser(string memory _identifier)
		public
		view
		returns(user memory){
			require(callerAllowed());
			require(callerAdmin());
			require(exists("User", _identifier));
			return(userMap[_identifier]);
		}

	function getApplication(string memory _identifier)
		public
		view
		returns(application memory){
			require(callerAllowed());
			require(callerAdmin());
			require(exists("Application", _identifier));
			return(applicationMap[_identifier]);
		}

	function getDevice(string memory _identifier)
		public
		view
		returns(device memory){
			require(callerAllowed());
			require(callerAdmin());
			require(exists("Device", _identifier));
			return(deviceMap[_identifier]);
		}

	function addrToStr(address _addr)
		private
		pure
		returns(string memory)
			{
				bytes32 value = bytes32(uint256(_addr));
				bytes memory characters = "0123456789abcdef";
				bytes memory str = new bytes(51);
				str[0] = '0';
				str[1] = 'x';
				for (uint256 i = 0; i < 20; i++) {
					str[2+i*2] = characters[uint8(value[i + 12] >> 4)];
					str[3+i*2] = characters[uint8(value[i + 12] & 0x0f)];
				}
				return string(str);
			}

	function compare(string memory str1, string memory str2)
		private
		pure
		returns(bool){
			return keccak256(abi.encodePacked(str1)) == keccak256(abi.encodePacked(str2));
		}

}