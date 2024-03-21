import axios from "axios";
import { ethers } from "ethers";
import Web3Modal from "web3modal";

const web3Modal = new Web3Modal({
	cacheProvider: true,
	providerOptions: {}, // add additional providers here, like WalletConnect, Coinbase Wallet, etc.
});

const register = async () => {
	const message = await axios.get("/codecanyon/Files/_web3/signature").then((res) => res.data);
	const provider = await web3Modal.connect();

	provider.on("accountsChanged", () => web3Modal.clearCachedProvider());

    const web3 = new ethers.BrowserProvider(provider);
    const address = await (await web3.getSigner()).getAddress();
    const signature = await (await web3.getSigner()).signMessage(message);
	
	axios.post("/codecanyon/Files/_web3/register", {
		address: address,
		signature: signature,
	}).then((response) => location.reload())
	.catch((error) => console.log(error));
};

window.register = register;