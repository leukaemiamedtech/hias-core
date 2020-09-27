pragma solidity ^0.7.1;
pragma experimental ABIEncoderV2;

// SPDX-License-Identifier: MIT

contract iotJumpWay {

	uint compensation = 1000000000000000000;
	address haccount = 0x1F4EFc2329a4047Bf2eCa01bb1d011382B0C355A;
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
			require(isHIAS());
			require(setup == false);
			authorized[msg.sender] = true;
			setup = true;
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
			require(accessAllowed(msg.sender));
			return hashMap[_identifier].exists == true;
		}

	function registerAuthorized(address _address)
		public {
			require(accessAllowed(msg.sender));
			authorized[_address] = true;
			compensate(msg.sender, compensation);
		}

	function deregisterAuthorized(address _address)
		public {
			require(accessAllowed(msg.sender));
			delete authorized[_address];
			compensate(msg.sender, compensation);
		}

	function registerHash(string memory dataId, bytes memory _dataHash, uint _time, uint _createdBy, string memory _identifier, address payable _address)
		public {
			require(accessAllowed(msg.sender));
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
			require(accessAllowed(msg.sender));
			require(hashExists(_identifier));
			return(hashMap[_identifier]);
		}

	function count()
		public
		view
		returns (uint){
			require(accessAllowed(msg.sender));
			return hashes;
		}

}