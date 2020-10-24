pragma solidity ^0.7.1;
pragma experimental ABIEncoderV2;

// SPDX-License-Identifier: MIT

contract HIASPatients {

	uint compensation = 1000000000000000000;
	address haccount = YourHiasApplicationAddress;
	bool setup = false;

	struct patient {
		address bcaddress;
		bool active;
		bool admitted;
		bool discharged;
		uint patient;
		uint application;
		uint created;
		uint createdBy;
		uint updated;
		bool exists;
	}

	uint patients = 0;
	uint users = 0;

	mapping (string => patient) patientMap;
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
			require(setup == false);
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
			require( amount <= address(this).balance,"Not enough balance") ;
			_address.transfer(amount);
		}

	function userAllowed()
		public
		view
		returns(bool) {
			return authorized[msg.sender];
		}

	function patientExists(string memory _identifier)
		public
		view
		returns(bool) {
			require(userAllowed(), "Access not allowed");
			return patientMap[_identifier].exists == true;
		}

	function patientActive(string memory _identifier)
		public
		view
		returns(bool) {
			require(userAllowed(), "Access not allowed");
			return patientMap[_identifier].active == true;
		}

	function patientAdmitted(string memory _identifier)
		public
		view
		returns(bool) {
			require(userAllowed(), "Access not allowed");
			return patientMap[_identifier].admitted == true;
		}

	function patientDischarged(string memory _identifier)
		public
		view
		returns(bool) {
			require(userAllowed(), "Access not allowed");
			return patientMap[_identifier].discharged == true;
		}

	function registerUser(address _address)
		public {
			require(userAllowed(), "Access not allowed");
			authorized[_address] = true;
			compensate(msg.sender, compensation);
		}

	function deregisterUser(address _address)
		public {
			require(userAllowed(), "Access not allowed");
			delete authorized[_address];
			compensate(msg.sender, compensation);
		}

	function registerPatient(string memory _identifier, address _address, bool _active, bool _admitted, bool _discharged, uint _patient, uint _application, uint _time, uint _createdBy)
		public {
			require(userAllowed(), "Access not allowed");
			patient memory newPatient = patient(_address, _active, _admitted, _discharged, _patient,  _application, _time, _createdBy, _time, true);
			patientMap[_identifier] = newPatient;
			patients++;
			compensate(msg.sender, compensation);
		}

	function getPatient(string memory _identifier)
		public
		view
		returns(patient memory){
			require(userAllowed(), "Access not allowed");
			require(patientExists(_identifier), "Patient does not exist");
			return(patientMap[_identifier]);
		}

	function updatePatient(string memory _identifier, address _address, bool _active, bool _admitted, bool _discharged, uint _patient, uint _application, uint _time)
		public {
			require(userAllowed(), "Access not allowed");
			require(patientExists(_identifier), "Patient does not exist");
			patient storage currentPatient = patientMap[_identifier];
			currentPatient.bcaddress = _address;
			currentPatient.active = _active;
			currentPatient.admitted = _admitted;
			currentPatient.discharged = _discharged;
			currentPatient.patient = _patient;
			currentPatient.application = _application;
			currentPatient.updated = _time;
			compensate(msg.sender, compensation);
		}

	function deregisterPatient(string memory _identifier)
		public {
			require(userAllowed(), "Access not allowed");
			require(patientExists(_identifier), "Patient does not exist");
			delete patientMap[_identifier];
			patients--;
			compensate(msg.sender, compensation);
		}
}