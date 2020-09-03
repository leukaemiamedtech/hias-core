# Asociacion De Investigacion En Inteligencia Artificial Para La Leucemia Peter Moss
## HIAS - Hospital Intelligent Automation System
[![HIAS - Hospital Intelligent Automation System](Media/Images/HIAS-Hospital-Intelligent-Automation-System.png)](https://github.com/LeukemiaAiResearch/HIAS)

[![VERSION](https://img.shields.io/badge/VERSION-0.8.0-blue.svg)](https://github.com/LeukemiaAiResearch/HIAS/tree/0.8.0) [![DEV BRANCH](https://img.shields.io/badge/DEV%20BRANCH-0.9.0-blue.svg)](https://github.com/LeukemiaAiResearch/HIAS/tree/0.9.0) [![Contributions Welcome!](https://img.shields.io/badge/Contributions-Welcome-lightgrey.svg)](CONTRIBUTING.md)  [![Issues](https://img.shields.io/badge/Issues-Welcome-lightgrey.svg)](issues) [![LICENSE](https://img.shields.io/badge/LICENSE-MIT-blue.svg)](LICENSE)

&nbsp;

# Table Of Contents

- [Introduction](#introduction)
- [Key Features](#key-features)
- [HIAS Network Map](#hias-network-map)
- [HIAS UI](#hias-ui)
- [HIAS Staff Users](#hias-users)
- [HIAS IoT Network](#hias-iot-network)
    - [HIAS IoT Network Zones](#hias-iot-network-zones)
    - [HIAS IoT Network Devices](#hias-iot-network-devices)
    - [HIAS IoT Network Sensors/Actuators](#hias-iot-network-sensors-actuators)
    - [HIAS IoT Network Applications](#hias-iot-network-applications)
    - [HIAS IoT Network Data](#hias-iot-network-data)
- [HIAS Facial Recognition](#hias-facial-recognition)
- [HIAS Natural Language Understanding Engines](#hias-natural-language-understanding-engines)
- [HIAS Data Analysis](#hias-data-analysis)
    - [COVID-19](#covid-19)
- [HIAS Detection Systems](#hias-detection-systems)
    - [COVID-19 Detection System (CNN)](#covid-19-detection-system-cnn)
    - [Acute Lymphoblastic Leukemia Detection System (CNN)](#covid-19-detection-system-cnn)
- [EMAR / EMAR Mini](#emar--emar-mini)
- [Installation](#installation)
- [Modular Addons](#modular-addons)
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
- **Local Samba Server (Complete)**
    - A local Samba file server allowing controlled individual and group access to files on your local network.
- **Local IoT Broker (Complete)**
    - Local and private MQTT/Websockets broker based on the  [iotJumpway Broker](https://github.com/iotJumpway/Broker "iotJumpway Broker").
- **Facial Identification Systems (Complete)**
    - Facial identification systems based on [GeniSysAI](https://github.com/GeniSysAI/ "GeniSysAI").
- **Natural Language Understanding (NLU) Server (Complete)**
    - Natural Language Understanding server based on [GeniSysAI](https://github.com/GeniSysAI/ "GeniSysAI").
- **COVID Data Analysis System (Complete)**
    - A data anaysis system for monitoring the COVID 19 pandemic. This system collects data from the [Johns Hopkins University COVID-19 Daily Reports](https://github.com/CSSEGISandData/COVID-19/) on Github.
- **AI Detection Systems (Complete)**
    - Detection systems for classsifying Acute Lymphoblastic Leukemia and COVID-19.
- **HIS/HMS (In Development)**
    - Hospital management system providing online tools for managing and running day to day activities and resources for the hospital.

&nbsp;

# HIAS Network Map
![HIAS Network Map](Media/Images/HIAS-Network.png)

&nbsp;

# HIAS UI
![HIAS UI](Media/Images/dashboard.png)

The HIAS UI is the central control panel for the server, and all of the modular devices and applications that can be installed on it.

&nbsp;

# HIAS Staff Users
![HIAS Staff Users](Media/Images/HIAS-Users.png)
HIAS staff users can be created using the HIS Staff system. Users can be granted admin privileges allowing them access to further restricted areas of the UI. Each user has a connected iotJumpWay application which can be used with custom applications and our [HIAS Android application](). The staff area has a built in API that let's applications communicate with the HIAS network using their staff credentials. One such example of this is the Natural Language Understanding API.

&nbsp;

# HIAS IoT Network
![HIAS IoT Network](Media/Images/HIAS-IoT-Dashboard.png)
The HIAS IoT network is powered by a new, fully open-source version of the [iotJumpWay](https://www.iotJumpWay.com "iotJumpWay"). The HIAS iotJumpway dashboard is your control panel for managing all of your network iotJumpWay zones, devices, sensors/actuators and applications.

The modular systems that we build to be compatible with this network will all create their own iotJumpWay applications etc during installation, you will be able to manage all of these applications and devices through the iotJumpWay dashboard.

A HIAS network is represented by an iotJumpWay location. Within each location you can have multiple zones, devices and applications.

## IoT Network Zones
![HIAS IoT Network Zones](Media/Images/HIAS-IoT-Zones.png)
iotJumpWay Zones represent a room or area within a location. For instance, in a hospital you may have zones such as *Reception*, *Waiting Room*, *Operating Room 1* etc.

![HIAS IoT Network Zones](Media/Images/HIAS-IoT-Zones-Edit.png)

## IoT Network Devices
![HIAS IoT Network Devices](Media/Images/HIAS-IoT-Devices.png)
iotJumpWay Devices represent physical devices on the network. Each device is attached to a location and zone, allowing staff to know where each of their devices are, soon all devices will publish their location to the system allowing for real-time tracking within the network.

![HIAS IoT Network Devices](Media/Images/HIAS-IoT-Devices-Edit.png)
## IoT Network Sensors/Actuators
![HIAS IoT Network Sensors/Actuators](Media/Images/HIAS-IoT-Sensors.png)
iotJumpWay Sensors & Actuators represent physical sensors and actuators included on network devices and allows direct communication with each sensor/actuator.

**This feature is still in development**

## IoT Network Applications
![HIAS IoT Network Applications](Media/Images/HIAS-IoT-Applications.png)
iotJumpWay Devices represent applications that can communicate with the  network. Each application is attached to a location, soon all applications will publish their location to the system allowing for real-time tracking.

![HIAS IoT Network Applications](Media/Images/HIAS-IoT-Applications-Edit.png)

## IoT Network Data
![HIAS IoT Network Data](Media/Images/HIAS-IoT-Data.png)
All data sent from devices and applications connected to the HIAS network is stored locally in a Mongo database (NoSQL). This means that staff can monitor all data on their network, and kall data stays on the network giving organizations total control of their data.

&nbsp;

# HIAS Facial Recognition
![HIAS Facial Recognition](Media/Images/HIAS-Facial-Recognition.png)
The HIAS facial recognition system is based on the [GeniSysAI](https://www.facebook.com/GeniSysAI/ "GeniSysAI") Vision projects. The facial recognition system uses web and IP cameras attached to devices on the network and processes frames from the cameras in real-time, before streaming the processed framed to a local server endpoint. Multiple GeniSysAI facial recognition devices can be configured. The cameras track known and unknown users and can communicate with the Natural Language Understanding Engines allowing conversations to be triggered based on facial recognition identifications.

![HIAS Facial Recognition](Media/Images/HIAS-Facial-Recognition-Edit.png)

&nbsp;

# HIAS Natural Language Understanding Engines
![HIAS Natural Language Understanding Engines](Media/Images/HIAS-NLU.jpg)
The HIAS UI allows Natural Language Understanding Engines to be connected to the network. These NLUs can be communicated with via the network allowing applications and devices to have realtime spoken interactions with known and unknown users.

&nbsp;

# HIAS Data Analysis
The HIAS network hosts a number of AI models that monitor data from local and external sources to make predictions based on the raw data. You can monitor real-time data using the HIAS UI.

## HIAS COVID-19 Data Analysis
![HIAS COVID-19 Data Analysis](Media/Images/HIAS-Data-Analysis-COVID-19.png)
Functionality is now available to set up a basic COVID-19 tracker that will power the graphs in the HIAS UI. This system pulls data from the [COVID-19 Data Repository by the Center for Systems Science and Engineering (CSSE) at Johns Hopkins University](https://github.com/CSSEGISandData/COVID-19 "COVID-19 Data Repository by the Center for Systems Science and Engineering (CSSE) at Johns Hopkins University") and displays the stats in the UI.

&nbsp;

# HIAS Detection Systems
Using AI models on the HIAS network, the UI can be used to classify image based samples for diseases such as COVID-19 and Leukemia.

## COVID-19 Detection System (CNN)
![HIAS COVID-19 Diagnosis (CNN)](Media/Images/HIAS-COVID-19-Detection-System.png)

 The HIAS COVID-19 Detection System (CNN) system uses the [COVID-19 Tensorflow DenseNet Classifier](https://github.com/COVID-19-AI-Research-Project/AI-Classification/tree/master/Projects/2 "COVID-19 Tensorflow DenseNet Classifier") project, a Tensorflow 2 DenseNet implementation using the [SARS-COV-2 Ct-Scan Dataset](https://www.kaggle.com/plameneduardo/sarscov2-ctscan-dataset "SARS-COV-2 Ct-Scan Dataset") by our collaborators, Plamenlancaster: [Professor Plamen Angelov](https://www.lancaster.ac.uk/lira/people/#d.en.397371) from [Lancaster University](https://www.lancaster.ac.uk/)/ Centre Director @ [Lira](https://www.lancaster.ac.uk/lira/), & his researcher, [Eduardo Soares PhD](https://www.lancaster.ac.uk/sci-tech/about-us/people/eduardo-almeida-soares).

 ## Acute Lymphoblastic Leukemia Detection System (CNN)
![HIAS COVID-19 Diagnosis (CNN)](Media/Images/HIAS-ALL-Detection-System.png)

The HIAS Acute Lymphoblastic Leukemia Detection System (CNN) is based on the proposed architecture in the [Acute Leukemia Classification Using Convolution Neural Network In Clinical Decision Support System](https://airccj.org/CSCP/vol7/csit77505.pdf) paper, using the [Acute Lymphoblastic Leukemia Image Database for Image Processing dataset](https://homes.di.unimi.it/scotti/all).

&nbsp;

# EMAR / EMAR Mini
![EMAR](Media/Images/HIAS-Robotics-EMAR.png)
Functionality to update, monitor and control [EMAR](https://github.com/COVID-19-AI-Research-Project/EMAR "EMAR")/[EMAR Mini](https://github.com/COVID-19-AI-Research-Project/EMAR-Mini "EMAR Mini") is now available. These features allow you to create EMAR/EMAR Mini devices, update the settings, monitor the camera streams and send commands to the robotic arm to move it.

![EMAR](Media/Images/HIAS-Robotics-EMAR-Edit.png)

![EMAR](Media/Images/HIAS-Robotics-EMAR-Edit-2.png)

![EMAR](Media/Images/HIAS-Robotics-EMAR-Edit-3.png)

&nbsp;

# Installation
Installation scripts and tutorials for setting up your HIAS - Hospital Intelligent Automation System & UI are provided. To get started, please follow the installation guides provided below in the order they are given:

| ORDER | GUIDE | INFORMATION | AUTHOR |
| ----- | ----- | ----------- | ------ |
| 1 | [Main Installation Guide](Documentation/Installation/Installation.md "Main Installation Guide") | Primary installation guide covering most of the information needed to do the core installation |  [Adam Milton-Barker](https://www.leukemiaresearchassociation.ai.com/team/adam-milton-barker "Adam Milton-Barker") |

&nbsp;

# Modular Addons
The HIAS network is made up of modular, intelligent devices. Below are some of the completed tutorials that can be used with the HIAS UI. Each project provides the details on how to connect them to the HIAS network, allowing them to controlled and monitored via the UI.

| GITHUB | README | INFORMATION | AUTHOR |
| ----- | ----- | ----------- | ------ |
| [COVID-19 AI Research Project](https://github.com/COVID-19-AI-Research-Project "COVID-19 AI Research Project") | [EMAR Mini](https://github.com/COVID-19-AI-Research-Project/EMAR-Mini "EMAR Mini") | EMAR Mini is a minature version of [EMAR](https://github.com/COVID-19-AI-Research-Project/EMAR "EMAR"), an open-source Emergency Robot Assistant to assist doctors, nurses and hospital staff during the COVID-19 pandemic, and similar situations we may face in the future. |  [Adam Milton-Barker](https://www.leukemiaresearchassociation.ai.com/team/adam-milton-barker "Adam Milton-Barker") |
| [COVID-19 AI Research Project](https://github.com/COVID-19-AI-Research-Project "COVID-19 AI Research Project") | [COVID-19 Tensorflow DenseNet Classifier For Raspberry Pi 4](https://github.com/COVID-19-AI-Research-Project/AI-Classification/tree/master/Projects/3 "COVID-19 Tensorflow DenseNet Classifier For Raspberry Pi 4") |  Uses DenseNet and [SARS-COV-2 Ct-Scan Dataset](https://www.kaggle.com/plameneduardo/sarscov2-ctscan-dataset "SARS-COV-2 Ct-Scan Dataset"), a large dataset of CT scans for SARS-CoV-2 (COVID-19) identification created by our collaborators, Plamenlancaster: [Professor Plamen Angelov](https://www.lancaster.ac.uk/lira/people/#d.en.397371) from [Lancaster University](https://www.lancaster.ac.uk/)/ Centre Director @ [Lira](https://www.lancaster.ac.uk/lira/), & his researcher, [Eduardo Soares PhD](https://www.lancaster.ac.uk/sci-tech/about-us/people/eduardo-almeida-soares) |  [Adam Milton-Barker](https://www.leukemiaresearchassociation.ai.com/team/adam-milton-barker "Adam Milton-Barker") |
| [Acute Myeloid & Lymphoblastic Leukemia AI Research Project](https://github.com/AMLResearchProject "Acute Myeloid & Lymphoblastic Leukemia AI Research Project") | [Acute Lymphoblastic Leukemia Tensorflow CNN For Raspberry Pi 4](https://github.com/AMLResearchProject/ALL-Tensorflow-2020/tree/master/RPI4 "Acute Lymphoblastic Leukemia Tensorflow CNN For Raspberry Pi 4") |  Uses an Acute Lymphoblastic Leukemia CNN based on the proposed architecture in the [Acute Leukemia Classification Using Convolution Neural Network In Clinical Decision Support System](https://airccj.org/CSCP/vol7/csit77505.pdf) paper, using the [Acute Lymphoblastic Leukemia Image Database for Image Processing dataset](https://homes.di.unimi.it/scotti/all). |  [Adam Milton-Barker](https://www.leukemiaresearchassociation.ai.com/team/adam-milton-barker "Adam Milton-Barker") |
| [Asociacion De Investigacion En Inteligencia Artificial Para La Leucemia Peter Moss](https://github.com/LeukemiaAiResearch "Asociacion De Investigacion En Inteligencia Artificial Para La Leucemia Peter Moss") | [GeniSysAI](https://github.com/LeukemiaAiResearch/GeniSysAI "GeniSysAI") |  HIAS GeniSysAI provides Natural Language Understanding & Facial Recognition applications for the HIAS network. The projects provided in this repository are based on the original GeniSysAI projects. |  [Adam Milton-Barker](https://www.leukemiaresearchassociation.ai.com/team/adam-milton-barker "Adam Milton-Barker") |

&nbsp;

# Acknowledgement
The template used for the UI in this project is a commercial template by  [Hencework](https://hencework.com/ "Hencework"). We have been granted permission from Hencework to use their template in this project and would to thank them for doing so. Please check out their [Portfolio](https://themeforest.net/user/hencework/portfolio "Portfolio") for more examples of their work.

&nbsp;

# Contributing

Asociacion De Investigacion En Inteligencia Artificial Para La Leucemia Peter Moss encourages and welcomes code contributions, bug fixes and enhancements from the Github community.

Please read the [CONTRIBUTING](CONTRIBUTING.md "CONTRIBUTING") document for a full guide to forking our repositories and submitting your pull requests. You will also find information about our code of conduct on this page.

## Contributors

- [Adam Milton-Barker](https://www.leukemiaresearchassociation.ai.com/team/adam-milton-barker "Adam Milton-Barker") - [Asociacion De Investigacion En Inteligencia Artificial Para La Leucemia Peter Moss](https://www.leukemiaresearchassociation.ai "Asociacion De Investigacion En Inteligencia Artificial Para La Leucemia Peter Moss") President/Founder & Intel Software Innovator, Sabadell, Spain

&nbsp;

# Versioning

We use SemVer for versioning. For the versions available, see [Releases](releases "Releases").

&nbsp;

# License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE "LICENSE") file for details.

&nbsp;

# Bugs/Issues

We use the [repo issues](issues "repo issues") to track bugs and general requests related to using this project. See [CONTRIBUTING](CONTRIBUTING.md "CONTRIBUTING") for more info on how to submit bugs, feature requests and proposals.