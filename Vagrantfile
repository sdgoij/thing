Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.provision :shell, path: "provision.sh"
  config.vm.network :forwarded_port, host: 8000, guest: 80
  config.vm.provider "docker" do |d|
    d.build_dir = "."
    d.ports = ["8000:80"]
  end
end
