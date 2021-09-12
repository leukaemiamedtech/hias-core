# Asociación de Investigacion en Inteligencia Artificial Para la Leucemia Peter Moss
# HIAS - Hospital Intelligent Automation Server

[![HIAS - Hospital Intelligent Automation System](assets/images/project-banner.jpg)](https://github.com/aiial/hias-core)

[![CURRENT RELEASE](https://img.shields.io/badge/CURRENT%20RELEASE-3.0.0-blue.svg)](https://github.com/aiial/hias-core/tree/release-3.0.0) [![UPCOMING RELEASE](https://img.shields.io/badge/DEV%20BRANCH-develop-blue.svg)](https://github.com/aiial/hias-core/tree/release-3.0.0) [![Issues](https://img.shields.io/badge/Issues-Welcome-lightgrey.svg)](issues) [![Bug Reports](https://img.shields.io/badge/Bug%20Reports-Welcome-lightgrey.svg)](https://github.com/aiial/hias-core/issues/new?assignees=&labels=&template=bug_report.md&title=) [![Contributions Welcome!](https://img.shields.io/badge/Contributions-Welcome-lightgrey.svg)](CONTRIBUTING.md)

[![Documentation Status](https://readthedocs.org/projects/hias-core/badge/?version=latest)](https://hias-core.readthedocs.io/en/latest/?badge=latest)
 [![CII Best Practices](https://bestpractices.coreinfrastructure.org/projects/5140/badge)](https://bestpractices.coreinfrastructure.org/projects/5140)

![Compliance Tests](https://img.shields.io/badge/Compliance%20Tests-TODO-red)
![Unit Tests](https://img.shields.io/badge/Unit%20Tests-TODO-red)
![Functional Tests](https://img.shields.io/badge/Functional%20Tests-TODO-red)

 [![LICENSE](https://img.shields.io/badge/LICENSE-MIT-blue.svg)](LICENSE) ![SemVer](https://img.shields.io/badge/semver-2.0.0-blue)

&nbsp;

# Introduction
HIAS Core is an open-source server designed to control and manage a network of intelligent IoT connected devices and applications.

The goal is to create an open-source platform that can be used by hospitals, clinics, and other medical organizations to control and manage their devices and applications.

The project is a work in progress and remains under development.

![HIAS Network](assets/images/hias-network-v3.jpg)

HIAS Core is made up of the following primary components:

- [HIASBCH](https://github.com/aiial/hiasbch) - A private Ethereum Blockchain
- [HIASCDI](https://github.com/aiial/hiascdi) - A private NGSI v2 Context Broker
- [HIASHDI](https://github.com/aiial/hiashdi) - A private Historical Data Broker
- iotJumpWay:
    - A Private MQTT Broker
    - A Private AMQP Broker
    - [HIAS MQTT IoT Agent](https://github.com/aiial/hias-mqtt-iot-agent)
    - [HIAS AMQP IoT Agent](https://github.com/aiial/hias-amqp-iot-agent)
    - [HIASBCH MQTT Blockchain Agent](https://github.com/aiial/hiasbch-mqtt-blockchain-agent)

HIAS network devices and applications are a range of open-source, modular devices and applications that can be provisioned via the HIAS UI. All HIAS compatible devices and applications have been developed by our volunteers and are completely open-source and free. These devices and applications currently include:

- Medical diagnostics applications.
- Data analysis applications.
- Computer Vision and Natural Language Understanding.
- Virtual and Mixed Reality applications.
- Robotics.
- Brain Computer Interface applications.

Users can also program their own devices and applications and connect them to the network.

&nbsp;

# HIAS UI

![HIAS UI - Dashboard](assets/images/hias-ui-dashboard.jpg)

The HIAS UI is the central control panel for the HIAS server and network.

![HIAS UI - HIASBCH](assets/images/hias-ui-hiasbch.jpg)

The UI provides the functionality to provision, manage, and monitor the HIAS network.

![HIAS UI - HIASBCH](assets/images/hias-ui-iot-agent.jpg)

&nbsp;

# Get Started
To get started follow the official HIAS Core documentation:

- [Installation Guide (Hyper-V)](https://hias.readthedocs.io/en/latest/installation/hyperv/)
- [Installation Guide (Virtual Box)](https://hias.readthedocs.io/en/latest/installation/virtualbox/)
- [Installation Guide (UBUNTU)](https://hias.readthedocs.io/en/latest/installation/ubuntu/)
- [Usage Guide (UBUNTU)](https://hias.readthedocs.io/en/latest/usage/ubuntu/)

&nbsp;

# Contributing
The Asociación de Investigacion en Inteligencia Artificial Para la Leucemia Peter Moss encourages and welcomes code contributions, bug fixes and enhancements from the Github community.

## Ways to contribute

The following are ways that you can contribute to this project:

- [Bug Report](https://github.com/aiial/hias-core/issues/new?assignees=&labels=&template=bug_report.md&title=)
- [Feature Request](https://github.com/aiial/hias-core/issues/new?assignees=&labels=&template=feature_request.md&title=)
- [Feature Proposal](https://github.com/aiial/hias-core/issues/new?assignees=&labels=&template=feature-proposal.md&title=)
- [Report Vulnerabillity](https://github.com/aiial/hias-core/issues/new?assignees=&labels=&template=report-a-vulnerability.md&title=)

Please read the [CONTRIBUTING](CONTRIBUTING.md "CONTRIBUTING") document for a full guide to forking our repositories and submitting your pull requests. You will find information about our code of conduct on the [Code of Conduct page](CODE-OF-CONDUCT.md "Code of Conduct page").

You can also join in with, or create, a discussion in our [Github Discussions](https://github.com/aiial/HIASCDI/discussions) area.

## Contributors

All contributors to this project are listed below.

- [Adam Milton-Barker](https://www.leukemiaairesearch.com/association/volunteers/adam-milton-barker "Adam Milton-Barker") - [Asociación de Investigacion en Inteligencia Artificial Para la Leucemia Peter Moss](https://www.leukemiaresearchassociation.ai "Asociación de Investigacion en Inteligencia Artificial Para la Leucemia Peter Moss") President/Founder & Lead Developer, Sabadell, Spain

&nbsp;

# Versioning
We use [SemVer](https://semver.org/) for versioning.

&nbsp;

# License
This project is licensed under the **MIT License** - see the [LICENSE](LICENSE "LICENSE") file for details.

&nbsp;

# Bugs/Issues
We use the [repo issues](issues "repo issues") to track bugs and general requests related to using this project. See [CONTRIBUTING](CONTRIBUTING.md "CONTRIBUTING") for more info on how to submit bugs, feature requests and proposals.
