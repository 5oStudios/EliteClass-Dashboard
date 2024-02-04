# Use the official Nginx image as a parent image
FROM nginx:latest

# Define your desired user ID
ARG USER_ID=1001
ARG USER=studios
ENV TZ=Asia/Kuwait

# Install necessary packages including libcap2-bin
RUN apt-get update && \
    apt-get install -y libcap2-bin && \
    rm -rf /var/lib/apt/lists/*

# Allow Nginx to bind to privileged ports as a non-root user
RUN setcap 'cap_net_bind_service=+ep' /usr/sbin/nginx

# Create a new group and user both named '5ostudios' with the specified ID
RUN addgroup --gid $USER_ID $USER \
    && adduser --disabled-login --no-create-home --uid $USER_ID --ingroup $USER $USER \
    && id $USER

# Change ownership of the Nginx web directories to the new user
RUN chown -R $USER:$USER /var/cache/nginx \
    && chown -R $USER:$USER /var/log/nginx \
    && chown -R $USER:$USER /etc/nginx/conf.d \
    && touch /var/run/nginx.pid \
    && chown -R $USER:$USER /var/run/nginx.pid

# Switch to non-root user
USER $USER
