pragma solidity ^0.7.1;
pragma experimental ABIEncoderV2;

// SPDX-License-Identifier: MIT

contract iotJumpWay {

	uint compensation = 1000000000000000000;
	address haccount = YourHiasApplicationAddress;
	bool setup = false;

	struct dataHash {
		bytes dataHash;
		uint created;
		uint createdBy;
		string publishedBy;
		bool exists;
	}

	uint hashes = 0;

	mapping (string => dataHash) hashMap;
	mapping (address => bool) private authorized;

	function isHIAS()
		private
		view
		returns(bool) {
			return msg.sender == haccount;
		}

	function initiate()
		public {
			require(isHIAS(), "Caller Not HIAS");
			require(setup == false, "Setup is not false");
			authorized[msg.sender] = true;
			setup = true;
		}

	function getBalance()
		public
		view
		returns (uint256) {
			require(isHIAS(), "Caller Not HIAS");
			return address(this).balance;
		}

	function deposit(uint256 amount)
		payable
		public {
			require(isHIAS(), "Caller Not HIAS");
			require(msg.value == amount);
		}

	function updateCompensation(uint amount)
		public {
			require(isHIAS(), "Caller Not HIAS");
			compensation = amount;
		}

	function compensate(address payable _address, uint256 amount)
		private {
			require(amount <= address(this).balance,"Not enough balance");
			_address.transfer(amount);
		}

	function accessAllowed(address _address)
		public
		view
		returns(bool) {
			return authorized[_address];
		}

	function hashExists(string memory _identifier)
		public
		view
		returns(bool) {
			require(accessAllowed(msg.sender), "Access not allowed");
			return hashMap[_identifier].exists == true;
		}

	function registerAuthorized(address _address)
		public {
			require(accessAllowed(msg.sender), "Access not allowed");
			authorized[_address] = true;
			compensate(msg.sender, compensation);
		}

	function deregisterAuthorized(address _address)
		public {
			require(accessAllowed(msg.sender), "Access not allowed");
			delete authorized[_address];
			compensate(msg.sender, compensation);
		}

	function registerHash(string memory dataId, bytes memory _dataHash, uint _time, uint _createdBy, string memory _identifier, address payable _address)
		public {
			require(accessAllowed(msg.sender), "Access not allowed");
			dataHash memory newHashMap = dataHash(_dataHash, _time, _createdBy, _identifier, true);
			hashMap[dataId] = newHashMap;
			hashes++;
			compensate(msg.sender, compensation);
			compensate(_address, compensation);
		}

	function getHash(string memory _identifier)
		public
		view
		returns(dataHash memory){
			require(accessAllowed(msg.sender), "Access not allowed");
			require(hashExists(_identifier), "Hash does not exist");
			return(hashMap[_identifier]);
		}

}