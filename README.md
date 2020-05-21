# Peter Moss Leukemia AI Research
## HIAS - Hospital Intelligent Automation System
[![HIAS - Hospital Intelligent Automation System](Media/Images/HIAS-Hospital-Intelligent-Automation-System.png)](https://github.com/LeukemiaAiResearch/HIAS)

[![VERSION](https://img.shields.io/badge/VERSION-0.1.0-blue.svg)](https://github.com/LeukemiaAiResearch/HIAS/tree/0.1.0) [![DEV BRANCH](https://img.shields.io/badge/DEV%20BRANCH-0.2.0-blue.svg)](https://github.com/LeukemiaAiResearch/HIAS/tree/0.2.0) [![Issues Welcome!](https://img.shields.io/badge/Contributions-Welcome-lightgrey.svg)](CONTRIBUTING.md)  [![Issues](https://img.shields.io/badge/Issues-Welcome-lightgrey.svg)](issues) [![LICENSE](https://img.shields.io/badge/LICENSE-MIT-blue.svg)](LICENSE)

&nbsp;

# Table Of Contents

- [Introduction](#introduction)
- [Key Features](#key-features)
- [HIAS Network Map](#hias-network-map)
- [HIAS UI](#hias-ui)
- [HIAS Users](#hias-users)
- [HIAS IoT Network](#hias-iot-network)
    - [HIAS IoT Network Zones](#hias-iot-network-zones)
    - [HIAS IoT Network Devices](#hias-iot-network-devices)
    - [HIAS IoT Network Sensors/Actuators](#hias-iot-network-sensors-actuators)
    - [HIAS IoT Network Applications](#hias-iot-network-applications)
    - [HIAS IoT Network Data](#hias-iot-network-data)
- [HIAS Facial Recognition](#hias-facial-recognition)
- [HIAS Data Analysis](#hias-data-analysis)
    - [COVID-19](#covid-19)
- [EMAR](#emar)
- [Installation](#installation)
- [Acknowledgement](#acknowledgement)
- [Contributing](#contributing)
    - [Contributors](#contributors)
- [Versioning](#versioning)
- [License](#license)
- [Bugs/Issues](#bugs-issues)

&nbsp;

# Introduction

The **Peter Moss Leukemia AI Research HIAS Network** is an open-source Hospital Intelligent Automation System. The system's server powers an intelligent network using a locally hosted, encrypted IoT server and proxy. 

The server UI provides the capabalities of managing a network of open-soruce intelligent devices and applications. These devices/applications and databases all run and communicate on the local network. This means that premises have more control and security when it comes to their hardware, data and storage.   

__This project is a proof of concept, and is still a work in progress, however our plan is to work with a local medical/health center or hospital to do a pilot project.__

&nbsp;

# Key Features

- **Local Web Server (Complete)** 
    - Locally hosted webserver using NGINX.
- **Proxy (Complete)**
    - Secure access to local devices from the outside world.
- **High Grade SSL Encryption (Complete)** 
    - High grade (A+) encryption for the web server, proxy and network.
- **Server UI (Work In Progress)** 
    - A control panel to monitor and manage your HIAS network.
- **Local Samba Server** 
    - A local Samba file server allowing controlled individual and group access to files on your local network.
- **Local IoT Broker (Complete)** 
    - Local and private MQTT/Websockets broker based on the  [iotJumpway Broker](https://github.com/iotJumpway/Broker "iotJumpway Broker").
- **Facial Identification Server (Complete)** 
    - Facial identification systems based on [tassAI](https://github.com/TASS-AI/Tass-History "tassAI").
- **Natural Language Understanding (NLU) Server (In Redevelopment)** 
    - Natural Language Understanding server based on [GeniSysAI](https://github.com/GeniSysAI/ "GeniSysAI").
- **HIS/HMS (In Redevelopment)** 
    - Hospital management system providing online tools for managing and running day to day activities and resources for the hospital.

&nbsp;

# HIAS Network Map
![HIAS Network Map](Media/Images/HIAS-Network.png) 

&nbsp;

# HIAS UI
![HIAS UI](Media/Images/dashboard.png)

The HIAS UI is the central control panel for the server, and all of the modular devices and applications that can be installed on it.

&nbsp;

# HIAS Users
![HIAS Users](Media/Images/HIAS-Users.png)

HIAS users can be created using the HIS Staff system. Users can be granted admin privileges allowing them access to further restricted areas of the UI. Each user has a connected iotJumpWay application which will later be used in our HIAS Android application.

&nbsp;

# HIAS IoT Network
![HIAS IoT Network](Media/Images/HIAS-IoT-Dashboard.png)

The HIAS IoT network is powered by a new, fully open-source version of the [iotJumpWay](https://www.iotJumpWay.com "iotJumpWay"). The HIAS iotJumpway dashboard is your control panel for managing all of your network iotJumpWay zones, devices, sensors/actuators and applications. 

The modular systems that we build to be compatible with this network will all create their own iotJumpWay applications etc during installation, you will be able to manage all of these applications and devices through the iotJumpWay dashboard. 

A HIAS network is represented by an iotJumpWay location. Within each location you can have multiple zones, devices and applications.

## HIAS IoT Network Zones
![HIAS IoT Network](Media/Images/HIAS-IoT-Zones.png)

iotJumpWay Zones represent a room or area within a location. For instance, in a hospital you may have zones such as *Reception*, *Waiting Room*, *Operating Room 1* etc.

## HIAS IoT Network Devices
![HIAS IoT Network](Media/Images/HIAS-IoT-Devices.png)

iotJumpWay Devices represent physical devices on the network. Each device is attached to a location and zone, allowing staff to know where each of their devices are, soon all devices will publish their location to the system allowing for real-time tracking within the network. 

## HIAS IoT Network Sensors/Actuators
![HIAS IoT Network](Media/Images/HIAS-IoT-Sensors.png)

iotJumpWay Sensors & Actuators represent physical sensors and actuators included on network devices and allows direct communication with each sensor/actuator. 

**This feature is still in development**

## HIAS IoT Network Applications
![HIAS IoT Network](Media/Images/HIAS-IoT-Applications.png)
iotJumpWay Devices represent applications that can communicate with the  network. Each application is attached to a location, soon all applications will publish their location to the system allowing for real-time tracking. 

## HIAS IoT Network Data
![HIAS IoT Network](Media/Images/HIAS-IoT-Data.png)
All data sent from devices and applications connected to the HIAS network is stored locally in a Mongo database (NoSQL). This means that staff can monitor all data on their network, and kall data stays on the network giving organizations total control of their data.

&nbsp;

# HIAS Facial Recognition
![HIAS Facial Recognition](Media/Images/HIAS-TASS.png)

The HIAS facial recognition system is based on [tassAI](https://www.facebook.com/TASSNetwork/ "tassAI"). The facial recognition system uses cameras attached to devices on the network and processes frames from the cameras in real-time, before streaming the processed framed to a local server endpoint. Multiple TASS devices can be configured and there will soon be integration with popular IP cameras like Foscam etc. 

&nbsp; 

# HIAS Data Analysis
![HIAS Facial Recognition](Media/Images/HIAS-Data-Analysis.png)

The HIAS network hosts a number of AI models and that monitor data from local and external sources to make predictions based on the raw data. You can monitor real-time data using the HIAS UI. 

## HIAS COVID-19 Data Analysis
![HIAS Facial Recognition](Media/Images/HIAS-Data-Analysis-COVID-19.png)

Functionality is now available to set up a basic COVID-19 tracker that will power the graphs in the HIAS UI. This system pulls data from the [COVID-19 Data Repository by the Center for Systems Science and Engineering (CSSE) at Johns Hopkins University](https://github.com/CSSEGISandData/COVID-19 "COVID-19 Data Repository by the Center for Systems Science and Engineering (CSSE) at Johns Hopkins University") and displays the stats in the UI. 

&nbsp;

# EMAR
![EMAR](Media/Images/EMAR-Dashboard.png) 

Basic functionality to update, monitor and control [EMAR](https://www.facebook.com/TASSNetwork/ "EMAR") is now available. The current features allow you to create EMAR devices, update the settings, monitor the camera stream and send commands to the robotic arm to move it. 

![EMAR](Media/Images/EMAR-Control.png)

&nbsp;

## Installation
Installation scripts and tutorials for setting up your HIAS - Hospital Intelligent Automation System & UI are provided. To get started, please follow the installation guides provided below in the order they are given:

| ORDER | GUIDE | INFORMATION | AUTHOR |
| ----- | ----- | ----------- | ------ |
| 1 | [Main Installation Guide](Documentation/Installation/Installation.md "Main Installation Guide") | Primary installation guide covering most of the information needed to do the core installation |  [Adam Milton-Barker](https://www.leukemiaresearchassociation.ai.com/team/adam-milton-barker "Adam Milton-Barker") |

&nbsp;

# Acknowledgement
The template used for the UI in this project is a commercial template by  [Hencework](https://hencework.com/ "Hencework"). We have been granted permission from Hencework to use their template in this project and would to thank them for doing so. Please check out their [Portfolio](https://themeforest.net/user/hencework/portfolio "Portfolio") for more examples of their work.

&nbsp;

# Contributing

The Peter Moss Acute Myeloid & Lymphoblastic Leukemia AI Research project encourages and welcomes code contributions, bug fixes and enhancements from the Github.

Please read the [CONTRIBUTING](CONTRIBUTING.md "CONTRIBUTING") document for a full guide to forking our repositories and submitting your pull requests. You will also find information about our code of conduct on this page.

## Contributors

- **AUTHOR:** [Adam Milton-Barker](https://www.leukemiaresearchassociation.ai.com/team/adam-milton-barker "Adam Milton-Barker") - [Peter Moss Leukemia AI Research](https://www.leukemiaresearchassociation.ai "Peter Moss Leukemia AI Research") Founder & Intel Software Innovator, Sabadell, Spain

&nbsp;

# Versioning

We use SemVer for versioning. For the versions available, see [Releases](releases "Releases").

&nbsp;

# License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE "LICENSE") file for details.

&nbsp;

# Bugs/Issues

We use the [repo issues](issues "repo issues") to track bugs and general requests related to using this project. See [CONTRIBUTING](CONTRIBUTING.md "CONTRIBUTING") for more info on how to submit bugs, feature requests and proposals.