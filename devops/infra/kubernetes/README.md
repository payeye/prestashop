Prestashop deployment for testshop, originaly based on the Prestashop Helm Chart by Bitnami:
https://github.com/helm/charts/blob/master/stable/prestashop/README.md

All of the manifests are deployed within the single kubernetes namespace: 
woocommerce-testshop

Each solution consist of:
Ingres config in a separate devops repo
Service (type NodePort)
PVC
Deployment
as well as two secrets that are not commited to the repo (example for dev3c env):
prestashop-dev3c
prestashop-externaldb-dev3c


The definitions of those secrets will not be commited to the repository for the security reasons.
Please find below templates (the passwords MUST be base64 encoded firs!):
```YAML
---
apiVersion: v1
kind: Secret
metadata:
  name: prestashop-dev3c
  namespace: "woocommerce-testshop"
  labels:
    app.kubernetes.io/name: prestashop
    helm.sh/chart: prestashop-15.3.6
    app.kubernetes.io/instance: prestashop-dev3c
    app.kubernetes.io/managed-by: Helm
type: Opaque
data:
  prestashop-password: "SuperSecretPass"
```

```YAML
---
apiVersion: v1
kind: Secret
metadata:
  name: prestashop-externaldb-dev3c
  namespace: "woocommerce-testshop"
  labels:
    app.kubernetes.io/name: prestashop
    helm.sh/chart: prestashop-15.3.6
    app.kubernetes.io/instance: prestashop-dev3c
    app.kubernetes.io/managed-by: Helm
type: Opaque
data:
  db-password: "SuperSecretPass"
```

Deployment procedure:
```bash
kubectl create -f namespace.yml
kubectl create -f secret_template.yml
```
and inside environemnt folder overlays/dev3c
```bash
kustomize build --load-restrictor=LoadRestrictionsNone --enable-helm | kubectl apply -f -
```
Once the application is deployed it must be additionaly configured:
```
One needs to explicitly turn on SSL in the Prestashop administration panel, else a `302` redirect to `http` scheme is returned on any page of the site by default.

To enable SSL on all pages, follow these steps:
  - Browse to the administration panel and log in.
  - Click “Shop Parameters” in the left navigation panel.
  - Set the option “Enable SSL” to “Yes”.
  - Click the “Save” button.
  - Set the (now enabled) option “Enable SSL on all pages” to “Yes”.
  - Click the “Save” button.
```