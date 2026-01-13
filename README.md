# StoryMapTeams

StoryMapTeams is a collaborative platform designed to allow multiple users to create, edit, and manage storymap projects in real-time.

## Overview

This project provides a web-based interface for visualizing and organizing narratives or project plans using a storymap structure. It is built to facilitate teamwork, ensuring that all contributors can participate in the editing process seamlessly.

## Key Features

- **Collaborative Editing**: Multiple users can work on the same storymap simultaneously.
- **Dynamic Editor**: A tailwind-powered interface for organizing and sorting story elements.
- **PHP Backend**: A simple and efficient backend for handling data storage and retrieval via JSON.
- **DDEV Support**: Local development environment pre-configured with DDEV.

## Getting Started

### Prerequisites

- [DDEV](https://ddev.readthedocs.io/) installed locally.

### Setup

1. Clone the repository.
2. Run `ddev start` to launch the local environment.
3. Access the project via the URL provided by DDEV (usually `https://storymaproot.ddev.site`).

## Project Structure

- `StoryMap/`: Main application directory.
  - `Edit/`: Contains the storymap editor interface.
  - `storymap_data/`: Directory where storymap JSON files are stored.
- `.ddev/`: Configuration files for the DDEV local environment.
